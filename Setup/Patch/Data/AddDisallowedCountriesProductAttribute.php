<?php

declare (strict_types=1);

namespace ML\DeveloperTest\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddDisallowedCountriesProductAttribute for Create Custom Product Attribute using Data Patch.
 */
class AddDisallowedCountriesProductAttribute implements DataPatchInterface
{

    /**
     * Attribute code
     *
     * @var string
     */
    const DISALLOWED_COUNTRIES = 'disallowed_countries';

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::DISALLOWED_COUNTRIES,
            [
                'group' => 'Product Sales Settings',
                'label' => 'Disallowed Countries',
                'type' => 'varchar',
                'input' => 'multiselect',
                'source' => '\Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture',
                'required' => false,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => false,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'visible_on_front' => false
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
