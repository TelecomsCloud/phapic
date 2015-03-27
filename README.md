# TelecomsCloud PHAPIC

This package provides an easy to use, guzzle based (http://guzzlephp.org/), client for the API.

## Installation

Clone the repository from Github

    git clone https://github.com/TelecomsCloud/phapic.git
    
Install the clients dependencies using composer (https://getcomposer.org)

    composer install

##Basic Use

```php
require_once __DIR__ . '/../vendor/autoload.php';

$apiBaseUri = 'https://api.telecomscloud.com/';
$sid = '...';
$token = '...';

$client = new Tc\Phapic\Client($apiBaseUri, $sid, $token);

var_dump($client->formatNumberE164(['number' => '01514962245', 'location' => 'GB']));
var_dump($client->formatNumberNational(['number' => '+441514962245', 'location' => 'GB']));
var_dump($client->formatNumberInternational(['number' => '01514962245', 'providedLocation' => 'GB', 'dialFromLocation' => 'US']));
var_dump($client->formatNumberPretty(['number' => '01514962245', 'location' => 'GB']));
```

```
array(1) {
  'number' =>
  string(13) "+441514962245"
}
array(1) {
  'number' =>
  string(11) "01514962245"
}
array(1) {
  'number' =>
  string(15) "011441514962245"
}
array(1) {
  'number' =>
  string(13) "0151 496 2245"
}
```
