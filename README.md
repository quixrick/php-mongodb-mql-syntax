# php-mongodb-mql-syntax
A PHP wrapper for using the native shell MongoDB Query Language to interact with MongoDB


## Initializing the connection
```php
require_once '../mql_to_php.php';
require_once '../config.php';

$mql = new MqlObj($settings['db']['db_host'], $settings['db']['db_name'], $settings['db']['db_user'], $settings['db']['db_pass'], $settings['db']['db_port']);
```

## Selecting a database and executing a find
```php
$mql->mql('use sample_restaurants');

print "<pre>"; print_r($mql->mql('db.restaurants.find({"address.zipcode": "11224"});')); print "</pre>";
```

## Selecting a database and executing a remove
```php
$mql->mql('use sample_restaurants');

print "<pre>"; print_r($mql->mql('db.restaurants.remove({name: "Riviera Caterer"});')); print "</pre>";
```
