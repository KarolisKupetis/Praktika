<?php
require 'vendor/autoload.php';


header('Acces-control-Allow-Origin');
header('Content-Type: application/json');

if (isset($_SERVER['REQUEST_METHOD'])) {
    $api = new \App\API\APIController();
    $api->requestFunction();

}
else{
    $logger = new \Monolog\Logger('info logger');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__.'/logger.log'));
    $ctr = new \App\Controller\Navigator($logger);
    $ctr->beginWork();
}

//$qry = new \App\database\QueryBuilder();
//echo $qry->
//select('posts', 'words')->
//from('words')->
//where('a','=','kapucino')->
//andWhere('b','=','ads')->
//andWhere('asdas' ,'>',"pokemon")->
//orWhere('a','=','b')->
//innerJoin('pips')->
//on('customer','<','name')->getQuery();

