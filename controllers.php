<?php

// namespace Controllers;

// use PDO;

class Controllers {
    public static function home() {

        // error_log('are we here?');
        // echo('are we here?');
        
        self::front_gate_check();
        
        echo 'Home Page Contents';

        echo "<br /><br />Session data:<br /><br />";
        // echo "uid: " . $_SESSION['uid'];

        echo var_export($_SESSION, false);

        // $test_result = authenticate_login('j@h.org', 'pass');
        // echo '<pre>$test_result = '; var_export($test_result, false); echo '</pre>';
    }

    /**
     * Either returns true, or redirects the user and halts execution
     */
    public static function front_gate_check() {
        // Check whether they've answered the age question at the front gate
        if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
            return true;
        } else {
            // Send the user to the front gate
            header('Location: ' . URL_BASE . '/front-gate');
            die();
        }
    }

    public static function signup_GET($msg = '', $un = '', $nickname = '') {

        require_once('templates/header.php');
        require_once('templates/signup.php');
        require_once('templates/footer.php');
    }

    public static function signup_POST() {

        $un = $_POST['un'];
        $pw = $_POST['pw'];
        $nickname = $_POST['nickname'];

        // Validate user name
        $un = substr($un, 0, 100);
        
        if (!filter_var($un, FILTER_VALIDATE_EMAIL)) {
            self::signup_GET('Invalid email address', $un, $nickname);
        }

        if(Db::userNameExists($un)) {
            self::signup_GET('Email address already in use', $un, $nickname);
        }

        // Validate password (maybe just for some absurd length to avoid intentional overrun attempts?)
        // Ultimately we're hashing it anyway.
        $pw = substr($pw, 0, 100);

        // Validate nickname
        $nickname = substr($nickname, 0, 50);
        if(preg_match('/[^a-zA-Z0-9\_\-]/mi', $nickname)) {
            self::signup_GET('Nickname can only contain letters, numbers, dashes and underscores', 
                $un, $nickname);
        }

        if(Db::nicknameExists($nickname)) {
            self::signup_GET('Nickname already in use', $un, $nickname);
        }

        self::signup_GET('Success', $un, $nickname);
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
            $_SESSION['uid'] = $auth[0]['user_id'];
            $_SESSION['user_name'] = $auth[0]['un'];
            $_SESSION['nickname'] = $auth[0]['nickname'];
            $_SESSION['account_created_at'] = $auth[0]['created_at'];
            $_SESSION['start'] = time(); // Taking now logged in time.
            
            // End session 30 minutes from the starting time.
            $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);

            header('Location: ' . URL_BASE);
            die();
        }

        // we should never get here
    }

    public static function front_gate_GET() {
        $html_page_title = "Strain Diary - Track Your Trees";
        $static_asset_url_base = "";
        $nonce = "12345";

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
            header('Location: ' . URL_BASE . '/login');
            die();
        } else {
            setcookie("passed_front_gate", "", time() - 3600); // set the expiration date to one hour ago
            header("Location: https://en.wikipedia.org/wiki/Legality_of_cannabis");
            die();
        }
    }
}
