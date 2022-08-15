<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Service;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use ML\DeveloperTest\Model\Config;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GeolocationService
{
    const COUNTRY_CODE_KEY = 'country_code';
    const COUNTRY_NAME_KEY = 'country_name';
    const API_REQUEST_URI = 'http://api.ipapi.com/api/';
    private RemoteAddress $remoteAddress;
    private Config $config;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private LoggerInterface $logger;

    public function __construct(
        RemoteAddress $remoteAddress,
        Config $config,
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        LoggerInterface $logger
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
    }

    private function getClientIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    public function getClientGeolocationData()
    {
        $response = null;
        try {
            $response = $this->doRequest($this->remoteAddress->getRemoteAddress(), [
                'query' => [
                    'access_key' => $this->config->getApiKey(),
                    'fields' => self::COUNTRY_CODE_KEY . ',' . self::COUNTRY_NAME_KEY
                ]
            ]);
        } catch (GuzzleException $e) {
            $this->logger->error('ML_DeveloperTest api connection error: ' . $e->getMessage(), ['context' => $e]);
        }

        if (!$response) {
            return null;
        }

        $status = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseBodyArray = json_decode((string)$responseBody, true);

        if ($status !== 200 || (isset($responseBodyArray['success']) && $responseBodyArray['success'] === false)) {
            $this->logger->error('Error from ML_Developertest module', ['context' => $responseBody]);

            return null;
        }


        return $responseBodyArray;
    }

    /**
     * Do API request with provided params
     *
     * @param string $uriEndpoint
     * @param array $params
     * @param string $requestMethod
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function doRequest(
        string $uriEndpoint,
        array $params = [],
        string $requestMethod = Request::HTTP_METHOD_GET
    ): ResponseInterface {
        /** @var Client $client */
        $client = $this->clientFactory->create([
            'config' => [
                'base_uri' => self::API_REQUEST_URI,
            ]
        ]);

        return $client->request($requestMethod, $uriEndpoint, $params);
    }
}
