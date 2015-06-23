<?php
namespace Tc\Phapic;


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

    protected $tokens;

    public function __construct($baseUri, StorageInterface $storage, $clientId, $clientSecret, $proxy = false)
    {
        $this->client = new Client($baseUri, $proxy);
        $this->storage = $storage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    protected function getAccessToken()
    {
        if (!isset($this->tokens)) {
            $tokens = $this->storage->getTokens();
        } else {
            $tokens = $this->tokens;
        }

        if ($tokens) {
            $currentDate = new DateTime('now');
            $expiresDate = new DateTime($tokens['expires_date']);
            $refreshExpiresDate = new DateTime($tokens['refresh_expires_date']);

            if ($expiresDate > $currentDate) {
                return $tokens['access_token'];
            }
        }

        $grantResponse = null;

        if (!$tokens || ($refreshExpiresDate < $currentDate)) {
            $authResponse = $this->oauth2AuthorizeCode('1', $this->clientId, 'abc123');
            $grantResponse = $this->oauth2GrantCode($authResponse['code'], $this->clientId, $this->clientSecret);
        } elseif ($expiresDate < $currentDate) {
            $grantResponse = $this->oauth2GrantRefresh($tokens['refresh_token'], $this->clientId, $this->clientSecret);
        }

        if (!$grantResponse) {
            throw new \Exception('There was a problem getting API authorization.');
        }

        $refreshExpiresSeconds = 28 * (24 * 60 * 60); // 28 Days

        $this->storage->setTokens(
            $grantResponse['access_token'],
            $grantResponse['expires_in'],
            $grantResponse['refresh_token'],
            $refreshExpiresSeconds
        );

        return $grantResponse['access_token'];
    }


    protected function oauth2AuthorizeCode($authorize, $clientId, $state)
    {
        return $this->client->oauth2AuthorizeCode(
            [
                'authorize' => $authorize,
                'client_id' => $clientId,
                'state' => $state
            ]
        );
    }


    protected function oauth2GrantCode($code, $clientId, $clientSecret)
    {
        return $this->client->oauth2GrantCode(
            [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ]
        );
    }


    protected function oauth2GrantRefresh($refreshToken, $clientId, $clientSecret)
    {
        return $this->client->oauth2GrantCode(
            [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ]
        );
    }

    public function formatNumberE164($number, $location, $pretty = false)
    {
        $this->client->setBearerToken($this->getAccessToken());

        $this->client->formatNumberE164(
            [
                'number' => $number,
                'location' => $location,
                'pretty' => $pretty
            ]
        );

    }


    public function formatNumberInternational($number, $providedLocation, $dialFromLocation, $pretty = false)
    {
        $this->client->setBearerToken($this->getAccessToken());

        $this->client->formatNumberInternational(
            [
                'number' => $number,
                'providedLocation' => $providedLocation,
                'dialFromLocation' => $dialFromLocation,
                'pretty' => $pretty
            ]
        );

    }


    public function formatNumberNational($number, $location, $pretty = false)
    {
        $this->client->setBearerToken($this->getAccessToken());

        $this->client->formatNumberNational(
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

        $this->client->formatNumberPretty(
            [
                'number' => $number,
                'location' => $location
            ]
        );
    }
}