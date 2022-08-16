<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Config ML_DeveloperTest class
 */
class Config
{
    /**
     * @var string
     */
    const XML_PATH_API_KEY = 'sales/disallowed_countries/api_key';

    /**
     * @var string
     */
    const XML_PATH_PRODUCT_SCOPE = 'sales/disallowed_countries/scope';

    /**
     * @var string
     */
    const XML_PATH_IS_MODULE_ENABLED = 'sales/disallowed_countries/enabled';

    /**
     * @var string
     */
    const XML_PATH_MESSAGE = 'sales/disallowed_countries/message';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_KEY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getProductScope()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_SCOPE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IS_MODULE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MESSAGE, ScopeInterface::SCOPE_STORE);
    }
}
