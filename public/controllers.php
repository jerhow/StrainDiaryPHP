<?php

namespace Controllers;

function home() {
    
    front_gate_check();
    
    echo 'Home Page Contents';

    echo "<br /><br />Session data:<br /><br />";
    echo "uid: " . $_SESSION['uid'];

    // echo var_export($_SESSION, false);
}

/**
 * Either returns true, or redirects the user and halts execution
 */
function front_gate_check() {
    // Check whether they've answered the age question at the front gate
    if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
        return true;
    } else {
        // Send the user to the front gate
        header("Location: /front-gate");
        die();
    }
}

function login_GET($msg = '') {

    require_once('templates/header.php');
    require_once('templates/login.php');
    require_once('templates/footer.php');
}

function login_POST() {

    $email = $_POST['email'];
    $pwd = $_POST['pwd'];

    if($email === 'abc' && $pwd === 'def') { // testing obvs...
        $_SESSION['uid'] = 'u1234';
        $_SESSION['start'] = time(); // Taking now logged in time.
        
        // End session 30 minutes from the starting time.
        $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);

        header("Location: /");
        die();
    } else {
        login_GET('Incorrect email or password');
    }

    // we should never get here
}

function front_gate_GET() {
    $html_page_title = "Strain Diary - Track Your Trees";
    $static_asset_url_base = "";
    $nonce = "12345";

    if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
        header("Location: /");
        die();
    }
    
    require_once('templates/header.php');
    require_once('templates/front-gate.php');
    require_once('templates/footer.php');
}

function front_gate_POST() {
    if( $_POST['front_gate_answer'] && $_POST['front_gate_answer'] === 'Yes' ) {
        setcookie('passed_front_gate', 'Yes', time() + (86400 * 30), "/"); // 86400 = 1 day
        header("Location: /");
        die();
    } else {
        setcookie("passed_front_gate", "", time() - 3600); // set the expiration date to one hour ago
        header("Location: https://en.wikipedia.org/wiki/Legality_of_cannabis");
        die();
    }
}
