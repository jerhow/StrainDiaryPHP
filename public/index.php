<?php
// phpinfo(); exit();

// Require composer autoloader
require '/app/vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();

// $router->setBasePath('/');

// Define routes
// ...

$router->get('foo', function() {
    echo 'Foo Page Contents';
});

$router->get('/', function() {
    echo 'Home Page Contents';
});

// This route handling function will only be executed when visiting http(s)://www.example.org/about
$router->get('/about', function() {
    echo 'About Page Contents';
});

// Run it!
$router->run();
