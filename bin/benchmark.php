<?php

require_once 'elasticWrapper.php';
require_once 'sqlWrapper.php';

$elastic = new ElasticWrapper();
$sql = new SqlWrapper();

//$time_start = microtime(true);
//$elastic->addData();
//echo 'elastic adding data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

//$time_start = microtime(true);
//$sql->addData();
//echo 'sql adding data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

$time_start = microtime(true);
$elastic->loadData();
echo 'elastic loading data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

$time_start = microtime(true);
$sql->loadData();
echo 'sql loading data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

//$time_start = microtime(true);
//$elastic->deleteData();
//echo 'elastic deleting data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

//$time_start = microtime(true);
//$sql->deleteData();
//echo 'sql deleting data: ' . number_format(microtime(true) - $time_start, 3) . PHP_EOL;

//$elastic->addData();
//print_r($elastic->loadData());
