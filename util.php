<?php

class Util {

    /**
     * Returns a boolean indicating success/failure
     */
    public static function send_confirmation_email($send_to = '', $nickname = '', $conf_code = '') {

        if($send_to === '') {
            error_log('send_confirmation_email(): No $send_to address provided');
            return false;
        }

        if($conf_code === '') {
            error_log('send_confirmation_email(): No $conf_code value provided');
            return false;
        }

        $api_key = getenv('SENDINBLUE_API_KEY', true);
        $conf_link = URL_DOMAIN . URL_BASE . "/confirmation/$conf_code";
        
        if(is_null($api_key) || !$api_key || $api_key === '') {
            error_log('send_confirmation_email(): No API key found');
            return false;
        }

        // Configure API key authorization: api-key
        $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $api_key);

        // Uncomment below line to configure authorization using: partner-key
        // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('partner-key', 'YOUR_API_KEY');

        $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new GuzzleHttp\Client(),
            $config
        );

        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail(); // \SendinBlue\Client\Model\SendSmtpEmail | Values to send a transactional email
        $sendSmtpEmail['to'] = array(array('email'=>$send_to, 'name'=>"New User - $nickname"));
        // $sendSmtpEmail['templateId'] = 59;
        $sendSmtpEmail['templateId'] = 1;
        $sendSmtpEmail['params'] = array(
            'nickname'=>$nickname, 
            'confirmation_link'=>$conf_link
        );
        // $sendSmtpEmail['headers'] = array('X-Mailin-custom'=>'custom_header_1:custom_value_1|custom_header_2:custom_value_2');

        // echo "SMTP object:<br />";
        // echo "<pre>";
        // var_export($sendSmtpEmail, false);
        // echo "</pre>";

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            // echo "RESULT object:<br />";
            // echo "<pre>";
            // var_export($result, false);
            // echo "</pre>";
        } catch (Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
            error_log('Exception when calling TransactionalEmailsApi->sendTransacEmail: ' . $e->getMessage() );
            return false;
        }

        return true;
    }

    /**
     * Returns a string
     */
    public static function confirmationCode() {
        $bytes = random_bytes(20);
        return bin2hex($bytes);
    }

    /**
     * Returns a number derived from the system's high resolution time
     * (integer on 64 bit platforms, float on 32 bit platforms)
     */
    public static function nonce() {
        return hrtime(true);
    }
}
