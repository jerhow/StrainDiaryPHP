<?php

// namespace Controllers;

// use PDO;

class Controllers {
    public static function root_GET($red_msg = '', $green_msg = '') {
        Util::front_gate_check();

        // var_export(session_status(), false);
        // var_export(PHP_SESSION_NONE, false);
        // var_export($_SESSION, false);

        require_once('templates/header.php');
        require_once('templates/root.php');
        require_once('templates/footer.php');
    }

    public static function home_GET() {
        Util::session_check();

        $user_id = $_SESSION['user_id'];
        $user_email = $_SESSION['user_name'];
        $nickname = $_SESSION['nickname'];
        $account_create_date = $_SESSION['account_created_at'];
        $msg = "";

        require_once('templates/header.php');
        require_once('templates/home.php');
        require_once('templates/footer.php');

    }

    public static function settings_GET($red_msg = '', $green_msg = '') {
        Util::session_check();

        $user_id = $_SESSION['user_id'];
        $user_email = $_SESSION['user_name'];
        $nickname = $_SESSION['nickname'];
        $account_create_date = $_SESSION['account_created_at'];

        require_once('templates/header.php');
        require_once('templates/settings.php');
        require_once('templates/footer.php');
    }

    public static function settings_POST() {
        Util::session_check();

        $field_being_edited = isset($_POST['field_being_edited']) 
            ? trim($_POST['field_being_edited']) 
            : '';
        $new_value = isset($_POST['new_value']) 
            ? trim($_POST['new_value']) 
            : '';

        // Validate what's being POSTed in
        $allowed_fields = ['user_email', 'nickname', 'pwd'];
        if(!in_array($field_being_edited, $allowed_fields)) {
            error_log("Invalid 'field_being_edited' value POSTed to Controllers::settings_POST()");
            self::settings_GET('ERROR: Invalid field parameter');
            return false;
        }

        if($field_being_edited === 'user_email') {
            // Vaildate email (AKA 'user_name')
            $new_value = substr($new_value, 0, 100);

            if($_SESSION['user_name'] === $new_value) {
                self::settings_GET(
                    'New email address is the same as your existing one - no changes made'
                );
                return false;
            }

            if (!filter_var($new_value, FILTER_VALIDATE_EMAIL)) {
                self::settings_GET("Invalid email address \"$new_value\" - no changes made");
                return false;
            }

            if(Db::userNameExists($new_value)) {
                self::settings_GET("Email address \"$new_value\" is already in use - no changes made");
                return false;
            }


            // Write update to DB
            if(Db::updateUserName($_SESSION['user_id'], $new_value)) {
                Util::logout();
                self::root_GET(
                    'Email address updated. Please check for an email from us, ' .
                    'as you must confirm this change to activate the new email address.'
                );
            } else {
                self::settings_GET(
                    "An error occurred when trying to update email address - no changes made"
                );
                return false;
            }

            // Dispatch transactional email to user about this change

            self::settings_GET('',
                'Email address updated. Please check for an email from us, ' .
                'as you must confirm this change to activate the new email address.'
            );
            return false;

        } elseif($field_being_edited === 'nickname') {
            // Vaildate nickname
            // Write update to DB
            // Dispatch transactional email to user about this change
            self::settings_GET('{ INSERT APPROPRIATE MESSAGE }');
            return false;           

        } elseif($field_being_edited === 'pwd') {
            // Vaildate password (field names: 'pwd' and 'pwd_verify')
            // Write update to DB
            // Dispatch transactional email to user about this change
            self::settings_GET('{ INSERT APPROPRIATE MESSAGE }');
            return false;

        } else {
             error_log('Hmm, we reached a place we should never be in Controllers::settings_POST()');
        }

        // echo var_export($_POST, false);
        echo '<pre>'; echo var_export($_SESSION, false); echo '</pre>';
        // echo var_export($field_being_edited, false);
        // echo var_export($new_value, false);
        die;
    }

    public static function logout_GET() {
        Util::logout();
        header('Location: ' . URL_BASE . '/');
        die();
    }

    /**
     * Confirming a newly-created account.
     * This is the place you land when you click through on the link
     * in the transactional email we send after you sign up for an account.
     */
    public static function confirmation_GET($conf_code = '') {

        $msg = "Oops, something's not right. This account may have " .
            "already been confirmed. <br /><br />" .
            "If so, you can simply sign in to get started.";

        $conf_code_matches = Db::check_confirmation_code($conf_code);

        if($conf_code_matches) {
            $account_confirmed = Db::mark_account_confirmed($conf_code);
            if($account_confirmed) {
                $msg = 'Account successfully confirmed!';
            }
        }

        require_once('templates/header.php');
        require_once('templates/confirmation.php');
        require_once('templates/footer.php');
    }

    public static function signup_GET($msg = '', $un = '', $nickname = '', $completed = false) {
        Util::front_gate_check();

        require_once('templates/header.php');
        require_once('templates/signup.php');
        require_once('templates/footer.php');
    }

    public static function signup_POST() {

        // TODO: Add POST array member existence validation (with default values)
        $un = $_POST['un'];
        $pw = $_POST['pw'];
        $nickname = $_POST['nickname'];

        // Validate user name
        $un = substr($un, 0, 100);
        
        if (!filter_var($un, FILTER_VALIDATE_EMAIL)) {
            self::signup_GET('Invalid email address', $un, $nickname);
            return false;
        }

        if(Db::userNameExists($un)) {
            self::signup_GET('Email address already in use', $un, $nickname);
            return false;
        }

        // Require a password (duh)
        if(trim($pw) === '') {
            self::signup_GET('A password is required', $un, $nickname);
            return false;
        }
        // Validate password (maybe just for some absurd length to avoid intentional overrun attempts?)
        // Ultimately we're hashing it anyway.
        $pw = substr($pw, 0, 100);

        // Validate nickname
        $nickname = substr($nickname, 0, 50);
        if(preg_match('/[^a-zA-Z0-9\_\-]/mi', $nickname)) {
            self::signup_GET('Nickname can only contain letters, numbers, dashes and underscores', 
                $un, $nickname);
            return false;
        }

        if(Db::nicknameExists($nickname)) {
            self::signup_GET('Nickname already in use', $un, $nickname);
            return false;
        }

        // If we get here, we've passed all the validations
        $confirmation_code = Util::confirmationCode();
        $hashed_pw = password_hash($pw, PASSWORD_BCRYPT, ["cost" => BCRYPT_COST]);
        Db::addNewUser($un, $hashed_pw, $nickname, $confirmation_code);

        $email_sent = Util::send_confirmation_email($un, $nickname, $confirmation_code);
        if(!$email_sent) {
            error_log("signup_POST() - email not sent");
        }

        self::signup_GET('Success', $un, $nickname, true);
        return true;
    }

    public static function login_GET($msg = '') {

        require_once('templates/header.php');
        require_once('templates/login.php');
        require_once('templates/footer.php');
    }

    public static function login_POST() {

        $un = $_POST['un'];
        $pw = $_POST['pw'];

        $auth = Db::authenticate($un, $pw);
        
        if(!$auth) {
            self::login_GET('Incorrect email or password');
        } else {
            $_SESSION['user_id'] = $auth[0]['user_id'];
            $_SESSION['user_name'] = $auth[0]['un'];
            $_SESSION['nickname'] = $auth[0]['nickname'];
            $_SESSION['account_created_at'] = $auth[0]['created_at'];
            $_SESSION['start'] = time(); // Taking now logged in time.
            
            // End session 30 minutes from the starting time.
            $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);

            header('Location: ' . URL_BASE . '/home');
            die();
        }

        // we should never get here
    }

    public static function front_gate_GET() {

        if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
            header('Location: ' . URL_BASE);
            die();
        }
        
        require_once('templates/header.php');
        require_once('templates/front-gate.php');
        require_once('templates/footer.php');
    }

    public static function front_gate_POST() {
        if( $_POST['front_gate_answer'] && $_POST['front_gate_answer'] === 'Yes' ) {
            setcookie('passed_front_gate', 'Yes', time() + (86400 * 30), "/"); // 86400 = 1 day
            header('Location: ' . URL_BASE . '/');
            die();
        } else {
            setcookie("passed_front_gate", "", time() - 3600); // set the expiration date to one hour ago
            header("Location: https://en.wikipedia.org/wiki/Legality_of_cannabis");
            die();
        }
    }
}
