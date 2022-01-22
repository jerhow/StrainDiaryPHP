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

    public static function login_GET($msg = '') {

        require_once('templates/header.php');
        require_once('templates/login.php');
        require_once('templates/footer.php');
    }

    public static function login_POST() {

        $un = $_POST['un'];
        $pw = $_POST['pw'];

        $auth = Common::authenticate($un, $pw);
        
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

/**
 * This is just a test function for database stuff. Not part of the app.
 */
function authenticate_login_($un ,$pw) {

    $db_host = getenv('DATABASE_HOST', true);
    $db_name = getenv('DATABASE_NAME', true);
    $db_user = getenv('DATABASE_USER', true);
    $db_pwd = getenv('DATABASE_PWD', true);

    echo '<pre>db_host = '; var_export($db_host, false); echo '</pre>';
    echo '<pre>db_name = '; var_export($db_name, false); echo '</pre>';
    echo '<pre>db_user = '; var_export($db_user, false); echo '</pre>';
    echo '<pre>db_pwd = '; var_export($db_pwd, false); echo '</pre>';

    echo '<pre>$un = '; var_export($un, false); echo '</pre>';

    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=UTF8";

    try {
        $dbh = new PDO($dsn, $db_user, $db_pwd);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die;
    }

    // set the PDO error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $user_id = 3;
        $sql = "SELECT pw FROM t_user WHERE un = :un";
        // echo '<pre>$sql = '; var_export($sql, false); echo '</pre>';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':un', $un);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    echo $rows[0]['pw'];
    // echo '<pre>$sql = '; var_export($sql, false); echo '</pre>';

    try {
        $user_id = 3;
        $sql = "select id, strain_name, stars, comments from t_user_strains where user_id = :user_id";
        echo '<pre>$sql = '; var_export($sql, false); echo '</pre>';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    foreach($rows as $row) {
        echo "<p>" . $row['id'] . " - " . $row['strain_name'] . "</p>";
    }

    // echo '<pre>rows = ';
    // var_export($rows, false);
    // echo '</pre>';


    $dbh = null;

    return true;

}
