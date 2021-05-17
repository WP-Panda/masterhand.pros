<?php
namespace PaymentCode;
use Exception;
use IHS_Send_Programmable_SMS;
use LogWriter\Log;
use Twilio\Rest\Client as TwilioClient;

class PaymentCode extends Base
{
    protected static $_instance = null;

    public function createPayCode($data = [])
    {
        Log::getInstance()->addLog(__METHOD__, 'call');
        Log::getInstance()->save('paycode', __DIR__);

        if(!empty($data['bid_id'])) {

            $exist = $this->db->get_var("SELECT code FROM {$this->tbCodes} WHERE bid_id = " . (int)$data['bid_id']);
            if(empty($exist)) {
                $code = self::generateCode();

                $this->db->insert($this->tbCodes, ['bid_id' => (int)$data['bid_id'], 'code' => $code]);

                $projectName = get_the_title($data['bid_id']);
                if (self::getOptNoticeSMS()) {
                    $this->noticeSMS($code, $projectName);
                }

                $this->noticeEmail($code, $projectName);
            }
        }
    }

    public function noticeEmail($code, $projectName = '')
    {
        global $user_ID;

        $subject = self::getOptSubjLetter();
        $message = ae_get_option('notify_payment_code');

        $message = str_replace('[project_name]', $projectName, $message);
        $message = str_replace('[payment_code]', $code, $message);
        $mail = \Fre_Mailing::get_instance();

        $author = get_userdata( $user_ID );
        return $mail->wp_mail($author->user_email, $subject, $message, array('user_id' => $user_ID));
    }

    public function noticeSMS($code, $projectName = '')
    {
        global $user_ID;

        $message = self::getOptMsgSMS();
        $message = str_replace('[project_name]', $projectName, $message);
        $message = str_replace('[payment_code]', $code, $message);

        // validation ^\+[1-9]\d{1,14}$

        $countryPhoneCode = get_user_meta($user_ID, 'ihs-country-code', true);
        $to = self::formToNumeric(get_user_meta($user_ID, 'user_phone', true));

        if(!empty($to) && !empty($countryPhoneCode)) {
            $twilio_sid_key = trim(get_option("ihs_twilio_sid_key"));
            $twilio_auth_token = trim(get_option("ihs_twilio_auth_token"));
            $twilio_phone_number = '+' . self::formToNumeric(get_option("ihs_twilio_phone_number"));

            //////////// test api //////////////
//            $user = 'ACce8b5402197ce3c6322cd98df5b86d85';
//            $pswd = 'afc63cf6a60e0b6793c64c6dc41356c5';
//            $from = '+15005550006';
            //////////// test api //////////////

            $user = ( ! empty( $twilio_sid_key ) ) ? $twilio_sid_key : '';
            $pswd = ( ! empty( $twilio_auth_token ) ) ? $twilio_auth_token : '';
            $from = ( ! empty( $twilio_phone_number ) ) ? $twilio_phone_number : '';
            $to = $countryPhoneCode . $to;

            try {
                $twilio = new TwilioClient($user, $pswd);

                $result = $twilio->messages->create(
                    $to,
                    [
                        'from' =>  $from,
                        'body' => $message
                    ]
                );
//            print($result->sid);';
//
//            $my_ihs_class = new IHS_Send_Programmable_SMS();
//            $my_ihs_class->ihs_send_msg_using_twilio( $sid, $token, $to, $twilio_mob_no, $message );
            } catch (Exception $e){

                Log::getInstance()->addLog(__METHOD__, $e->getMessage());
                Log::getInstance()->save('paycode', __DIR__);

            }
        }
    }

    public function getCode($bid_id = 0)
    {
        return $this->db->get_var("SELECT code FROM {$this->tbCodes} WHERE bid_id = {$this->toInt($bid_id)} AND used = 0");
    }

    public function getData($bid_id = 0)
    {
        $data = $this->db->get_row("SELECT pc.*, p.post_status as bid_status FROM {$this->tbCodes} pc 
        LEFT JOIN {$this->db->posts} p ON p.ID = pc.bid_id WHERE pc.bid_id = {$this->toInt($bid_id)}", ARRAY_A);

        $data['granted'] = ($data['bid_status'] == 'complete')? 0 : 1;

        return $data;
    }

    public function getStatusOrder($bid_id = 0)
    {
        return $this->db->get_var("SELECT p.post_status FROM {$this->db->posts} p
        WHERE p.ID = (SELECT m.meta_value FROM {$this->db->postmeta} m WHERE m.meta_key = 'fre_bid_order' AND m.post_id = {$this->toInt($bid_id)})");
    }

    public function getStatusBid($bid_id = 0)
    {
        return $this->db->get_var("SELECT p.post_status FROM {$this->db->posts} p WHERE p.post_type = 'bid' AND p.ID = {$this->toInt($bid_id)})");
    }

    public function setUsed($bid_id = 0)
    {
        return $this->db->update($this->tbCodes, ['used' => 1], ['bid_id' => $this->toInt($bid_id)]);
    }

    public function checkAccess($userId = 0, $bid_id = 0)
    {
        $authorBid = $this->db->get_var("SELECT p.post_author FROM {$this->tbCodes} c 
        LEFT JOIN {$this->db->posts} p ON p.ID = c.bid_id WHERE c.bid_id = {$this->toInt($bid_id)}");

        return ($userId == $authorBid);
    }

    public function isValid($code1, $code2)
    {
        $code1 = self::formToNumeric($code1);
        $code2 = self::formToNumeric($code2);

        return ($code1 === $code2);
    }

    public static function formToNumeric($str = '')
    {
        return !empty($str) && is_string($str)? preg_replace('/[^0-9]/', '', trim($str)) : '';
    }

    public static function generateCode($length = 8, $separator = '-', $after = 2)
    {
        $chars = '1234567890';
        $numChars = strlen($chars);
        $string = '';
        $_after = 0;
        for ($i=0; $i<$length; $i++) {
            $str = substr($chars, rand(1, $numChars) - 1, 1);

            $string.= ($_after == $after)? $separator . $str : $str;

            if($_after == $after){
                $_after = 0;
            }

            $_after++;
        }
        return $string;
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}