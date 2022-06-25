<?php



require_once '../mql_to_php.php';
require_once '../config.php';






// CONNECT TO THE INSTANCE
$mql = new MqlObj($settings['db']['db_host'], $settings['db']['db_name'], $settings['db']['db_user'], $settings['db']['db_pass'], $settings['db']['db_port']);






// SELECT A DATABASE
$mql->mql('use sample_restaurants');






// RUN A FIND QUERY
// print "<pre>"; print_r($mql->mql('db.grades.find({student_id: 0, class_id: 39});')); print "</pre>";
print "<pre>"; print_r($mql->mql('db.restaurants.find({"address.zipcode": "11224"});')); print "</pre>";