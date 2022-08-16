<?php

declare(strict_types=1);

namespace ML\DeveloperTest\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use ML\DeveloperTest\Api\GeolocalizatorInterface;
use ML\DeveloperTest\Model\Config;
use ML\DeveloperTest\Service\Api\Geolocalizator;
use ML\DeveloperTest\Setup\Patch\Data\AddDisallowedCountriesProductAttribute;

/**
 * QuotePlugin class
 */
class QuotePlugin
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
     * @var Config
     */
    private Config $config;
    private Geolocalizator $geolocalizator;

    /**
     * @param Product $productResource
     * @param Configurable $configurable
     * @param Config $config
     */
    public function __construct(
        Product $productResource,
        Configurable $configurable,
        Config $config,
        GeolocalizatorInterface $geolocalizator
    ) {
        $this->productResource = $productResource;
        $this->configurable = $configurable;
        $this->config = $config;
        $this->geolocalizator = $geolocalizator;
    }

    /**
     * @param Quote $subject
     * @param Product|mixed $product
     * @param float|DataObject|null $request
     * @param string|null $processMode
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ): array {
        if (!$this->config->isModuleEnabled()) {
            return [$product, $request, $processMode];
        }

        $geolocationData = $this->geolocalizator->getClientGeolocationData();

        if (!$geolocationData) {
            return [$product, $request, $processMode];
        }

        $productToCheck = $this->getProduct($product, $request);

        if ($this->isProductDisallowed($productToCheck, $geolocationData[Geolocalizator::COUNTRY_CODE_KEY])) {
            $message = str_replace([
                strtoupper(Geolocalizator::COUNTRY_CODE_KEY),
                strtoupper(Geolocalizator::COUNTRY_NAME_KEY)
            ],
                [
                    $geolocationData[Geolocalizator::COUNTRY_CODE_KEY],
                    $geolocationData[Geolocalizator::COUNTRY_NAME_KEY]
                ],
                $this->config->getMessage());

            throw new LocalizedException(__($message));
        }

        return [$product, $request, $processMode];
    }

    /**
     * Get configurable or simple product according to module settings
     *
     * @param Product $product
     * @param $request
     * @return Product|null
     */
    public function getProduct(Product $product, $request): ?Product
    {
        if ($this->config->getProductScope() === Configurable::TYPE_CODE) {
            return $product;
        }

        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $request->getData();
            $attributes = $request['super_attribute'];
            $product = $this->configurable->getProductByAttributes($attributes, $product);
        }

        return $product;
    }

    /**
     * Check if product is sellable for specific country code
     *
     * @param Product|null $product
     * @param string|null $countryCode
     * @return bool|null
     */
    private function isProductDisallowed(?Product $product, ?string $countryCode): ?bool
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
