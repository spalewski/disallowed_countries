<?php
declare(strict_types=1);

namespace ML\DeveloperTest\Observer;

use Magento\Catalog\Model\ResourceModel\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use ML\DeveloperTest\Model\Config;
use ML\DeveloperTest\Service\GeolocationService;
use ML\DeveloperTest\Setup\Patch\Data\AddDisallowedCountriesProductAttribute;

/**
 * AddToCartObserver class
 */
class AddToCartObserver implements ObserverInterface
{
    /**
     * @var Product
     */
    private Product $productResource;

    /**
     * @var Configurable
     */
    private Configurable $configurable;

    /**
     * @var GeolocationService
     */
    private GeolocationService $geolocationService;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Product $productResource
     * @param Configurable $configurable
     * @param GeolocationService $geolocationService
     * @param Config $config
     */
    public function __construct(
        Product $productResource,
        Configurable $configurable,
        GeolocationService $geolocationService,
        Config $config
    ) {
        $this->productResource = $productResource;
        $this->configurable = $configurable;
        $this->geolocationService = $geolocationService;
        $this->config = $config;
    }

    /**
     * Observer checks product added to cart if it's sellable for customer country
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isModuleEnabled()) {
            $product = $this->getProduct($observer);
            $geolocationData = $this->geolocationService->getClientGeolocationData();

            if (!$geolocationData) {
                return;
            }

            if ($this->isProductDisallowed($product, $geolocationData[GeolocationService::COUNTRY_CODE_KEY])) {
                $message = str_replace([
                    strtoupper(GeolocationService::COUNTRY_CODE_KEY),
                    strtoupper(GeolocationService::COUNTRY_NAME_KEY)
                ],
                    [
                        $geolocationData[GeolocationService::COUNTRY_CODE_KEY],
                        $geolocationData[GeolocationService::COUNTRY_NAME_KEY]
                    ],
                    $this->config->getMessage());

                throw new LocalizedException(__($message));
            }
        }
    }

    /**
     * Get configurable or simple product according to module settings
     *
     * @param Observer $observer
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct(Observer $observer): ?\Magento\Catalog\Model\Product
    {
        /**@var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');
        $request = $observer->getData('info');

        if ($this->config->getProductScope() !== Configurable::TYPE_CODE) {
            return $product;
        }

        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $attributes = $request['super_attribute'];
            $product = $this->configurable->getProductByAttributes($attributes, $product);
        }

        return $product;
    }

    /**
     * Check if product is sellable for specyfic country code
     *
     * @param \Magento\Catalog\Model\Product|null $product
     * @param string|null $countryCode
     * @return bool|null
     */
    private function isProductDisallowed(?\Magento\Catalog\Model\Product $product, ?string $countryCode): ?bool
    {
        if (!$product || !$countryCode) {
            return null;
        }
        $disallowedCountries = $product->getData(AddDisallowedCountriesProductAttribute::DISALLOWED_COUNTRIES);

        if (!$disallowedCountries) {
            return null;
        }

        $disallowedCountries = explode(',', $disallowedCountries);

        return in_array($countryCode, $disallowedCountries);
    }
}
