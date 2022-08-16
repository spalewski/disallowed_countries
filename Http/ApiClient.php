<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Webapi\Rest\Request;
use ML\DeveloperTest\Service\Api\Geolocalizator;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class ApiClient implements ApiClientInterface
{
    private RemoteAddress $remoteAddress;
    private ClientFactory $clientFactory;
    private LoggerInterface $logger;

    public function __construct(
        ClientFactory $clientFactory,
        LoggerInterface $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    public function get(array $data = []): ?ResponseInterface
    {
        /** @var Client $client */
        $client = $this->clientFactory->create(
            $data['url']
        );

        try {
            $response = $client->request(Request::HTTP_METHOD_GET, $data['ip'], $data['params']);
        } catch (GuzzleException $exception) {
            $response = null;
            $this->logger->error('ML_DeveloperTest api connection error: ' . $exception->getMessage(),
                ['context' => $exception]);
        }

        return $response;
    }
}
