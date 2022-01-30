<?php

ini_set('display_errors', '1');
ini_set('session.cookie_httponly', 1);

require_once 'vendor/autoload.php'; // Composer autoloader
require_once 'config.php';
require_once 'util.php';
require_once 'db.php';
require_once 'controllers.php';

$dbh = Db::dbh();

main();

$dbh = null;

function main() {

    global $dbh;

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
        // header('Location: ' . URL_BASE . '/');
    }

    $router = new \Bramus\Router\Router();

    // $router->setBasePath('/straindiary');

    $router->get('/', 'Controllers::root_GET');

    $router->get('/front-gate', 'Controllers::front_gate_GET');
    $router->post('/front-gate', 'Controllers::front_gate_POST');

    $router->get('/login', 'Controllers::login_GET');
    $router->post('/login', 'Controllers::login_POST');

    $router->get('/signup', 'Controllers::signup_GET');
    $router->post('/signup', 'Controllers::signup_POST');

    $router->get('/confirmation/{conf_code}', 'Controllers::confirmation_GET');

    $router->get('/home', 'Controllers::home_GET');

    $router->get('/logout', 'Controllers::logout_GET');

    $router->run();

    return true;
}
