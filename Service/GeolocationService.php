<?php
declare(strict_types=1);

namespace ML\DeveloperTest\Service;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use ML\DeveloperTest\Model\Config;
use Psr\Log\LoggerInterface;

/**
 *
 */
class GeolocationService
{
    /**
     * @var string
     */
    const COUNTRY_CODE_KEY = 'country_code';

    /**
     * @var string
     */
    const COUNTRY_NAME_KEY = 'country_name';

    /**
     * @var string
     */
    const API_REQUEST_URI = 'http://api.ipapi.com/api/';

    /**
     * @var RemoteAddress
     */
    private RemoteAddress $remoteAddress;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param RemoteAddress $remoteAddress
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @return false|string
     */
    private function getClientIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }


    /**
     * @return false|mixed
     */
    public function getClientGeolocationData()
    {
        //TODO implement GuzzleHttp or other with detailed error handling
        $ch = curl_init(self::API_REQUEST_URI . $this->remoteAddress->getRemoteAddress(). '?access_key=' . $this->config->getApiKey() . '&fields=' . self::COUNTRY_CODE_KEY . ',' . self::COUNTRY_NAME_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $api_result = json_decode($json, true);

        if ($api_result === false) {
            $this->logger->error('Error from ML_Developertest module. Curl connection error');
        }

        return $api_result;
    }
}
