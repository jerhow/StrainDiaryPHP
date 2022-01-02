<?php

namespace Controllers;

function home() {
    echo 'Home Page Contents';
}

function front_gate_GET() {
    $foo = 'HELLO!!!';
    $html_page_title = "Strain Diary - Track Your Trees";
    $static_asset_url_base = "";
    $nonce = "12345";
    
    require_once('templates/header.php');
    require_once('templates/front-gate.php');
    require_once('templates/footer.php');
}
