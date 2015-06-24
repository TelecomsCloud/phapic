# TelecomsCloud PHAPIC

This package provides an easy to use, guzzle based (http://guzzlephp.org/), client for the API.

Requires:
  PHP >= 5.4

## Installation

Clone the repository from Github

    git clone https://github.com/TelecomsCloud/phapic.git
    
Install the clients dependencies using composer (https://getcomposer.org)

    composer install

## Basic Use

```php

require_once __DIR__ . '/../vendor/autoload.php';

$baseUri = 'https://api.telecomscloud.com';
$clientId = '...';
$clientSecret = '...';

$dsn = '...';
$dbUsername = '...';
$dbPassword = '...';


$pdo = new PDO($dsn, $dbUsername, $dbPassword);
$storage = new \Tc\Phapic\PdoStorageInterface($clientId, $clientSecret, $pdo);

$phapic = new \Tc\Phapic\Phapic($baseUri, $storage, $clientId, $clientSecret);

var_dump($phapic->formatNumberE164('01515554202', 'GB'));
var_dump($phapic->formatNumberE164('01515554202', 'GB', '1'));

```

```
array(1) {
  ["number"]=>
  string(16) "+441515554202"
}

array(1) {
  ["number"]=>
  string(16) "+44 151 555 4202"
}

```
