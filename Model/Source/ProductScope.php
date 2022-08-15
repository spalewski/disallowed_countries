<?php
declare(strict_types=1);

namespace ML\DeveloperTest\Model\Source;

use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * ProductScope source class
 */
class ProductScope implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Configurable::TYPE_CODE, 'label' => __('Configurable')],
            ['value' => Type::DEFAULT_TYPE, 'label' => __('Simple')],
        ];
    }
}
