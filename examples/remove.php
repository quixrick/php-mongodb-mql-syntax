<?php



require_once '../mql_to_php.php';
require_once '../config.php';






// CONNECT TO THE INSTANCE
$mql = new MqlObj($settings['db']['db_host'], $settings['db']['db_name'], $settings['db']['db_user'], $settings['db']['db_pass'], $settings['db']['db_port']);






// SELECT A DATABASE
$mql->mql('use skyblock');






// RUN A REMOVE QUERY
print "<pre>"; print_r($mql->mql('db.sky.remove({name: "Golden Plate"});')); print "</pre>";