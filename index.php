<?php

ini_set('display_errors', '1');

// Require Composer autoloader
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'db.php';
require_once 'controllers.php';
require_once 'common.php';

// phpinfo(); die;

// var_export($_ENV, false); die;

// function enabled() {
//     return in_array('mysql', PDO::getAvailableDrivers());
// }

// enabled();

// echo '<pre>';
// for($x = 0; $x < 10; $x++) {
//     $pw_hashed = password_hash('pass', PASSWORD_BCRYPT, ["cost" => 8]);
//     echo '<br />';
//     echo $pw_hashed;
//     $verified = password_verify('pass', $pw_hashed);
//     echo '<br />';
//     var_export($verified, false);
//     echo '<br />'; echo '<br />';
// }
// echo '</pre>';
// die;

$dbh = Db::dbh();

main();

function main() {

    global $dbh;

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $router = new \Bramus\Router\Router();

    // $router->setBasePath('/straindiary');

    $router->get('/', 'Controllers::home');
    $router->get('/front-gate', 'Controllers::front_gate_GET');
    $router->post('/front-gate', 'Controllers::front_gate_POST');

    $router->get('/login', 'Controllers::login_GET');
    $router->post('/login', 'Controllers::login_POST');

    $router->get('/signup', 'Controllers::signup_GET');
    $router->post('/signup', 'Controllers::signup_POST');

    $router->run();

    if(isset($dbh) && !is_null($dbh)) {
        $dbh = null;
    }

    return true;
}
