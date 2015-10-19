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

```

## Account Information

```

var_dump($phapic->accountInfo());

array(5) {
  'username' =>
  string(13) "UserName"
  'primary_email' =>
  string(22) "user@host.com"
  'credit_balance' =>
  double(10.00)
  'active_services' =>
  int(1)
  'status' =>
  string(2) "ok"
}

```


## Number Formatting


```

var_dump($phapic->formatNumberE164('01514961800', 'GB'));

array(1) {
  ["number"]=>
  string(16) "+441514961800"
}

var_dump($phapic->formatNumberE164('01514961800', 'GB', '1'));

array(1) {
  ["number"]=>
  string(16) "+44 151 496 1800"
}

```


# Inbound Fax


```

$lastPointer = 0;

var_dump($phapic->getFaxInfo($lastPointer));

array(1) {
  'records' =>
  array(1) {
    [0] =>
    array(7) {
      'id' =>
      string(15) "2987747483v0a59"
      'from_number' =>
      string(7) "Unknown"
      'service_number' =>
      string(11) "01514961800"
      'page_count' =>
      int(2)
      'received_date' =>
      string(24) "2015-09-14T14:45:48+0100"
      'tiff_bytes' =>
      int(79786)
      'pointer' =>
      int(2567425622)
    }
  }
}

$id = '2987747483v0a59'

var_dump($phapic->getInboundFax($id));

array(1) {
  'downloadLink' =>
  string(271) "https://...."
}
```
## Outbound Fax


$to = '01514961800';
$from = '01514961801';
$document =  "/root/to/document";
$postFile = new PostFile('fax_document',fopen($document, 'r'),'Fax-Document.pdf',['Content-Type' => 'application/pdf']);

var_dump($phapic->sendFax($to, $from, $postFile));

array(1) {
  'id' =>
  string(32) "45RT46567DSSDFGGS68FH897643RRF"
}

$faxId = '45RT46567DSSDFGGS68FH897643RRF';

var_dump($phapic->getOutboundFaxStatus($faxId));

 array(3) {
  'submitted_date' =>
  string(19) "2015-10-19 12:00:00"
  'status' =>
  string(4) "Sent"
  'status_code' =>
  int(3)
}

$faxPointer = '0';

var_dump($phapic->getOutboundFaxUpdates($faxPointer));

array(1) {
  'updates' =>
  array(3) {
    [0] =>
    array(5) {
      'id' =>
      string(32) "45RT46567DSSDF736G8FH897643RRF"
      'date' =>
      string(19) "2015-10-15 15:45:00"
      'status' =>
      string(4) "Sent"
      'status_code' =>
      int(3)
      'pointer' =>
      string(11) "78483275672"
    }
    [1] =>
    array(5) {
      'id' =>
      string(32) "45RT46567DSSDF736G8FH897643432"
      'date' =>
      string(19) "2015-10-15 17:00:00"
      'status' =>
      string(4) "Sent"
      'status_code' =>
      int(3)
      'pointer' =>
      string(11) "2246264245"
    }
    [2] =>
    array(5) {
      'id' =>
      string(32) "45R35JDS67DSSDF736G8FH34764332"
      'date' =>
      string(19) "2015-10-19 11:30:00"
      'status' =>
      string(4) "Sent"
      'status_code' =>
      int(3)
      'pointer' =>
      string(11) "12432234563"
    }
  }
}


```


## Outbound SMS


```

$to = "01514961800";
$from = "01514961801";
$message = "text";

var_dump($phapic->sendSms($to, $from, $message));


array(1) {
  'sms_id' =>
  string(32) "ADASD534521A326245525TFDSAFA"
}

$smsId = "ADASD534521A326245525TFDSAFA";


var_dump($phapic->getSmsStatus($smsId));

array(9) {
  'id' =>
  string(32) "ADASD534521A326245525TFDSAFA"
  'status' =>
  string(6) "queued"
  'submit_datetime' =>
  string(19) "2015-09-12 12:00:00"
  'dispatch_datetime' =>
  NULL
  'from' =>
  string(13) "+441514961801"
  'to' =>
  string(13) "+441514961800"
  'receipt_received_datetime' =>
  NULL
  'receipt_status_code' =>
  NULL
  'status_code' =>
  int(1)
}


```