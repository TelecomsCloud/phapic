<?php
namespace Tc\Phapic;

use GuzzleHttp\Client as GuzClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

/**
 * Class Client
 *
 * Guzzle based client for the Telecoms Cloud API
 *
 * @package TcApi
 *
 * @method formatNumberE164
 * @method formatNumberNational
 * @method formatNumberInternational
 * @method formatNumberPretty
 *
 * @method oauth2GrantClient
 *
 * @method accountInfo
 *
 * @method getFaxInfo
 * @method getInboundFax
 *
 * @method getSmsStatus
 * @method sendSms
 *
 * @method sendFax
 * @method getOutboundFaxStatus
 * @method getOutboundFaxUpdates
 */
class Client extends GuzzleClient
{
    const API_VERSION = 1;

    /** @var GuzClient $guzzleClient */
    protected $guzzleClient;

    /**
     * @param string $baseUri TelecomsCloud API base URI, usually https://api.telecomscloud.com
     * @param bool $proxy optional http proxy address for development & testing
     */
    public function __construct($baseUri, $proxy = false)
    {
        $config = [];

        if ($proxy) {
            $config['default']['proxy'] = $proxy;
            $config['default']['debug'] = true;
        }

        $serviceDescription = json_decode(file_get_contents(__DIR__ . '/tc-service.json'), true);

        $description = new Description($serviceDescription);

        $client = new GuzClient(['base_url' => $baseUri]);
        $client->setDefaultOption('allow_redirects', false);

        if ($proxy) {
            $client->setDefaultOption('proxy', $proxy);
        }

        $this->guzzleClient = $client;

        parent::__construct($client, $description, $config);
    }


    public function setBearerToken($accessToken)
    {
        $this->guzzleClient->setDefaultOption('headers/authorization', 'Bearer ' . $accessToken);
    }
}
