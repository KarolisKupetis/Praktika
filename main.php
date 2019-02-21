<?php
require 'vendor/autoload.php';


header('Acces-control-Allow-Origin');
header('Content-Type: application/json');

//$work = new \App\Controller\Navigator();
//$work->beginWork();


if (isset($_SERVER['REQUEST_METHOD'])) {
    $api = new \App\API\APIController();
    $api->requestFunction();

}
else{
    $ctr = new \App\Controller\Navigator();
    $ctr->beginWork();
}
