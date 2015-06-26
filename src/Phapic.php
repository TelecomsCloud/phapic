<?php
namespace Tc\Phapic;


use DateInterval;
use DateTime;
use GuzzleHttp\Exception\ClientException;

class Phapic
{
    /** @var Client $client */
    protected  $client;

    /** @var StorageInterface $storage */
    protected $storage;

    protected $clientId;

    protected $clientSecret;

    protected $token;

    public function __construct($baseUri, StorageInterface $storage, $clientId, $clientSecret, $proxy = false)
    {
        $this->client = new Client($baseUri, $proxy);
        $this->storage = $storage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    protected function getAccessToken()
    {
        if (!isset($this->token)) {
            $getToken = $this->storage->getToken();
            
            if ($getToken) {
		$this->token = $getToken;
            }
        }

        if ($this->token) {
            $expiresDate = new DateTime($this->token['expires_date']);
        }

        $currentDate = new DateTime('now');
    
        if (!is_array($this->token) || (isset($expiresDate) && $expiresDate < $currentDate)) {
            $grantResponse = $this->oauth2GrantClient($this->clientId, $this->clientSecret);

            if (!$grantResponse) {
                throw new \Exception('There was a problem getting API authorization.');
            }

            $expiresDate = $this->nowPlusSeconds($grantResponse['expires_in']);
 
            $this->storage->setToken(
               $grantResponse['access_token'],
               $expiresDate->format('Y-m-d H:i:s')
            );

            $this->token = [
                'access_token' => $grantResponse['access_token'],
                'expires_in' => $expiresDate->format('Y-m-d H:i:s')
            ];
        }

        return $this->token['access_token'];
    }


    protected function nowPlusSeconds($seconds)
    {
        $dateTime = new DateTime('now');
        $dateTime->add(new DateInterval('PT' . $seconds . 'S'));

        return $dateTime;
    }


    protected function oauth2GrantClient($clientId, $clientSecret)
    {
        return $this->client->oauth2GrantClient(
            [
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ]
        );
    }


    public function formatNumberE164($number, $location, $pretty = '0')
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->client->formatNumberE164(
            [
                'number' => $number,
                'location' => $location,
                'pretty' => $pretty
            ]
        );

    }


    public function formatNumberInternational($number, $providedLocation, $dialFromLocation, $pretty = '0')
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->client->formatNumberInternational(
            [
                'number' => $number,
                'providedLocation' => $providedLocation,
                'dialFromLocation' => $dialFromLocation,
                'pretty' => $pretty
            ]
        );

    }


    public function formatNumberNational($number, $location, $pretty = '0')
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->client->formatNumberNational(
            [
                'number' => $number,
                'location' => $location,
                'pretty' => $pretty
            ]
        );

    }


    public function formatNumberPretty($number, $location)
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->client->formatNumberPretty(
            [
                'number' => $number,
                'location' => $location
            ]
        );
    }


    public function accountInfo()
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->client->accountInfo();
    }


    public function faxInboundInfo($lastPointer)
    {
        $this->client->setBearerToken($this->getAccessToken());

        return $this->$client->faxInboundInfo(
            [
                'last_pointer' => $lastPointer
            ]

        );

    }


    public function  faxInboundId($id)
    {
        $this->$client->setBearerToken($this->getAccessToken());

        return $this->$client->faxInboundId(
          [
              'id'=>$id
          ]

        );

    }

}
