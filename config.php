<?php

define('BCRYPT_COST', 8);
define('HTML_PAGE_TITLE', 'Strain Diary - Track Your Trees');

$env = getenv('SD_ENV', true);

if(!$env) {
    $env = 'DEV';
}

if($env === 'DEV') {
    define('URL_BASE', '/straindiary');
    define('STATIC_ASSET_URL_BASE', '');
} elseif($env === 'PROD') {
    define('URL_BASE', '');
    define('STATIC_ASSET_URL_BASE', '');
} else {
    define('URL_BASE', '/straindiary');
    define('STATIC_ASSET_URL_BASE', '');
}
