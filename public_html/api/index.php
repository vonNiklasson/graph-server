<?php

require_once(__DIR__ . '/../../backend/config.php');

$config['displayErrorDetails'] = true;
//$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

try {
    $app->run();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}
