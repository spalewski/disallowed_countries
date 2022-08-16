<?php

namespace ML\DeveloperTest\Service\Api;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use ML\DeveloperTest\Api\GeolocalizatorInterface;
use ML\DeveloperTest\Http\ApiClientInterface;
use ML\DeveloperTest\Http\Request\BuilderInterface;
use ML\DeveloperTest\Http\Request\BuilderInterfaceFactory;
use ML\DeveloperTest\Model\Config;
use Psr\Log\LoggerInterface;

class Geolocalizator implements GeolocalizatorInterface
{
    const COUNTRY_CODE_KEY = 'country_code';
    const COUNTRY_NAME_KEY = 'country_name';
    const API_REQUEST_URI = 'http://api.ipapi.com/api/';

    private RemoteAddress $remoteAddress;
    private Config $config;
    private LoggerInterface $logger;
    private BuilderInterfaceFactory $requestBuilder;
    private ApiClientInterface $apiClient;

    public function __construct(
        RemoteAddress $remoteAddress,
        Config $config,
        LoggerInterface $logger,
        BuilderInterfaceFactory $requestBuilder,
        ApiClientInterface $apiClient
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
        $this->logger = $logger;
        $this->requestBuilder = $requestBuilder;
        $this->apiClient = $apiClient;
    }

    /**
     * @return mixed|null
     */
    public function  getClientGeolocationData()
    {
        /** @var BuilderInterface $request */
        $request = $this->requestBuilder->create();
        $request->addUrl( self::API_REQUEST_URI);
        $request->addAccessKey($this->config->getApiKey());
        $request->addIp($this->remoteAddress->getRemoteAddress());
        $request->addFields([self::COUNTRY_CODE_KEY, self::COUNTRY_NAME_KEY]);
        $request->addTimeOut((int)$this->config->getTimeout());
        $request->addConnectTimeOut((int)$this->config->getConnectionTimeout());

        $response = $this->apiClient->get($request->getRequestData());

        if (!$response) {
            return null;
        }

        $status = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseBodyArray = json_decode((string)$responseBody, true);

        if ($status !== 200 || (isset($responseBodyArray['success']) && $responseBodyArray['success'] ===  false)) {
            $this->logger->error('ML_DeveloperTest api response error', ['context' => $responseBody]);

            return null;
        }

        return $responseBodyArray;
    }
}
