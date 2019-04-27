<?php

define('BASE_DIR', __DIR__ . '/../');

require_once BASE_DIR . 'vendor/autoload.php';
require_once BASE_DIR . 'generated-conf/config.php';

/* Make sure to not break anything if .env doesn't exist */
try {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
} catch (Exception $exception) {}

/**
 * Whether the application is in debug mode or not.
 */
define('DEBUG', (getenv('DEBUG') === 'true'));
/* If were' in debug mode we can show errors */
if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    //error_reporting(E_ALL); // TODO: Check which value should be set here
}

function start_session() {
    /* Make sure the session is started for each request */
    session_name('sess_id');
    session_start();
}
