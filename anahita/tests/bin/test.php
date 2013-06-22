<?php 

$init_file     = realpath(dirname(__FILE__).'/../lib/init.php');;
$test_folder   = realpath(dirname(__FILE__).'/../units/*.php');
$report_folder = realpath(dirname(__FILE__).'/../reports');
array_shift($argv);
if ( count($argv) ) {
    $test_folder = array_shift($argv);
}
print `phpunit --stop-on-failure  --verbose --bootstrap $init_file $test_folder`;
?>