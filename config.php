<?php

define('BCRYPT_COST', 8);
define('HTML_PAGE_TITLE', 'Strain Diary - Track Your Trees');

$env = getenv('SD_ENV', true);

if(!$env) {
    $env = 'DEV';
}

if($env === 'DEV') {
    define('URL_DOMAIN', 'http://127.0.0.1');
    define('URL_BASE', '/straindiary');
    define('STATIC_ASSET_URL', 'https://straindiary.nyc3.digitaloceanspaces.com/');
} elseif($env === 'PROD') {
    define('URL_DOMAIN', 'https://straindiary.com');
    define('URL_BASE', '');
    define('STATIC_ASSET_URL', 'https://straindiary.nyc3.digitaloceanspaces.com/');
} else {
    define('URL_DOMAIN', 'http://127.0.0.1');
    define('URL_BASE', '/straindiary');
    define('STATIC_ASSET_URL', 'https://straindiary.nyc3.digitaloceanspaces.com/');
}
