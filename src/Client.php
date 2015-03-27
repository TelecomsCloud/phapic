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
 * @method check
 * @method echo
 * @method formatNumberE164
 * @method formatNumberNational
 * @method formatNumberInternational
 * @method formatNumberPretty
 */
class Client extends GuzzleClient
{
    const API_VERSION = 1;

    /**
     * @param string $baseUri TelecomsCloud API base URI, usually https://api.telecomscloud.com
     * @param string $sid a valid account sid
     * @param string $token a valid account token
     * @param bool $proxy optional http proxy address for development & testing
     */
    public function __construct($baseUri, $sid, $token, $proxy = false)
    {
        $config = [];

        if ($proxy) {
            $config['default']['proxy'] = $proxy;
            $config['default']['debug'] = true;
        }

        $serviceDescription = json_decode(file_get_contents(__DIR__ . '/tc-service.json'), true);

        $description = new Description($serviceDescription);

        $client = new GuzClient(['base_url' => $baseUri]);
        $client->setDefaultOption('auth', [$sid, $token]);

        parent::__construct($client, $description, $config);
    }
}
