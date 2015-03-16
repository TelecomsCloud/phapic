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
$apiSid = '...';
$apiToken = '...';

$client = new Client($apiBaseUri, $sid, $token);

var_dump($client->check());

var_dump($client->echo(['myString' => 'test']));
```

```
[TODO: SHOW SCRIPT OUTPUT EXAMPLE HERE]
```
