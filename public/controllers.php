<?php

namespace Controllers;

function home() {
    // Check whether they've answered the age question at the front gate
    if(isset($_COOKIE['passed_front_gate']) && $_COOKIE['passed_front_gate'] === 'Yes') {
        // User can stay here
    } else {
        // Send the user to the front gate
        header("Location: /front-gate");
        die();
    }
    
    echo 'Home Page Contents';
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
