<?php
/**
 * File so that we get translation
 */

$xmlStr = '<MESSAGE>
<AUTHKEY>' . $auth_key . '</AUTHKEY>
<SENDER>' . $sender_id . '</SENDER>
<ROUTE>4</ROUTE>
<COUNTRY>' . $country_code . '</COUNTRY>
<UNICODE>1</UNICODE>
<SMS TEXT="' . $message . '" >
<ADDRESS TO="' . $mob_number . '"></ADDRESS>
</SMS>
</MESSAGE>';

$url="http://api.msg91.com/api/v2/sendsms";

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => "$url",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $xmlStr,
	CURLOPT_HTTPHEADER => array(
		"authkey: $auth_key",
		"content-type: application/xml"
	),
));
