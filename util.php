<?php

class Util {

    public static function session_check() {
        if (session_status() === PHP_SESSION_NONE || empty($_SESSION['user_id'])) {
            session_start();
            header('Location: ' . URL_BASE . '/');
        }
    }

    public static function logout() {
        // Initialize the session.
        // If you are using session_name("something"), don't forget it now!
        // session_start();

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        // if (ini_get("session.use_cookies")) {
        //     $params = session_get_cookie_params();
        //     setcookie(session_name(), '', time() - 42000,
        //         $params["path"], $params["domain"],
        //         $params["secure"], $params["httponly"]
        //     );
        // }

        // Finally, destroy the session.
        session_destroy();
    }

    /**
     * Returns true, otherwise redirects
     */
    public static function front_gate_check() {
        // Check whether they've answered the age question at the front gate
        if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
            return true;
        } else {
            header('Location: ' . URL_BASE . '/front-gate');
            die();
        }
        
        // Should never get here
        return false;
    }

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
