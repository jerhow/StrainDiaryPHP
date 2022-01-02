<?php

// Require Composer autoloader
require_once '/app/vendor/autoload.php';
require_once 'controllers.php';

main();

function main() {

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $router = new \Bramus\Router\Router();

    $router->get('/', '\Controllers\home');
    $router->get('/front-gate', '\Controllers\front_gate_GET');
    $router->post('/front-gate', '\Controllers\front_gate_POST');

    $router->get('/login', '\Controllers\login_GET');
    $router->post('/login', '\Controllers\login_POST');

    $router->run();

    return true;
}
