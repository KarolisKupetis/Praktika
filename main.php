<?php
require 'vendor/autoload.php';

header('Acces-control-Allow-Origin');
header('Content-Type: application/json');

parse_str(file_get_contents('php://input'), $_PUT);

if (isset($_SERVER['REQUEST_METHOD'])) {
    $api = new \App\Controller\APIController();
    $api->requestFunction();
}
else{
    $logger = new \Monolog\Logger('info logger');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/logger.log'));
    $ctr = new \App\Controller\NavigationController($logger);
    $ctr->beginWork();
}











