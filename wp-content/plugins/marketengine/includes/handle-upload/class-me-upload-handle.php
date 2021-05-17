<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ME Upload File Handle
 *
 * Class handle file upload by user
 *
 * @version     1.1
 * @package     Includes/Uploader
 * @author      Dakachi
 * @category    Class
 */
class ME_Upload_Handle
{
    /**
     * Get the file type marketengine support for each filename purpose
     * @param string $filename The file name 
     * @return array
     * @since 1.1
     */
    public static function support_file_type($filename)
    {
        $file_types = array(
            'dispute_file'    => 'psd,jpg,jpeg,gif,png,pdf,doc,docx,xlsx,xls,zip',
            'message_file'    => 'jpg,jpeg,gif,png,pdf,doc,docx,xlsx,xls,zip',
            'listing_gallery' => 'jpg,jpeg,gif,png',
            'user_avatar'     => 'jpg,jpeg,gif,png',
            'default'         => 'jpg,jpeg,gif,png',
        );

        $file_types = apply_filters('marketengine_support_file_types', $file_types);
        if (isset($file_types[$filename])) {
            return explode(',', $file_types[$filename]);
        } else {
            return array();
        }

    }

    public static function init_hooks()
    {
        add_action('wp_ajax_upload_multi_file', array(__CLASS__, 'upload_multi_file'));
        add_action('wp_ajax_upload_single_file', array(__CLASS__, 'upload_single_file'));
    }

    public static function upload_multi_file()
    {
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'marketengine')) {
            $filename   = $_REQUEST['filename'];
            $file       = $_FILES[$filename];
            $attachment = self::handle_file($file, $filename);
            if ($attachment) {
                if (!$_REQUEST['is_file']) {
                    marketengine_get_template('upload-file/multi-image-form', array(
                        'image_id' => $attachment['id'],
                        'filename' => $filename,
                        'close'    => true,
                    ));
                } else {
                    marketengine_get_template('upload-file/multi-file', array(
                        'image_id' => $attachment['id'],
                        'filename' => $filename,
                        'close'    => true,
                    ));
                }

            }

        }
        exit;
    }

    public static function upload_single_file()
    {
        if (!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'marketengine')) {
            $filename   = $_REQUEST['filename'];
            $file       = $_FILES[$filename];
            $attachment = self::handle_file($file, $filename);
            $close      = intval($_REQUEST['removable']);

            if ($attachment && $filename == 'message_file') {
                $message_data = array(
                    'listing_id' => $_REQUEST['listing_id'],
                    'content'    => '[me_message_file id=' . $attachment['id'] . ' ]',
                    'inquiry_id' => $_REQUEST['inquiry_id'],
                );
                $result  = ME_Inquiry_Handle::insert_message($message_data);
                $message = marketengine_get_message($result);
                marketengine_get_template('inquiry/message-item', array('message' => $message));
                exit;
            }

            if ($attachment) {

                if (!$_REQUEST['is_file']) {
                    marketengine_get_template('upload-file/single-image-form', array(
                        'image_id' => $attachment['id'],
                        'filename' => $filename,
                        'close'    => true,
                    ));
                } else {
                    marketengine_get_template('upload-file/single-file', array(
                        'image_id' => $attachment['id'],
                        'filename' => $filename,
                        'close'    => true,
                    ));
                }
            }

        }
        exit;
    }

    public static function handle_file($file, $filename)
    {
        $return = false;

        $me_support_file_types = self::support_file_type($filename);
        $file_type             = wp_check_filetype($file['name'], get_allowed_mime_types());
        if (!in_array($file_type['ext'], $me_support_file_types)) {
            return false;
        }

        $uploaded_file = wp_handle_upload($file, array('test_form' => false));

        if (isset($uploaded_file['file'])) {
            $file_loc  = $uploaded_file['file'];
            $file_name = basename($file['name']);

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );

            $attach_id   = wp_insert_attachment($attachment, $file_loc);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $return = array('data' => $attach_data, 'id' => $attach_id);

            return $return;
        }
        return $return;
    }

}
ME_Upload_Handle::init_hooks();
