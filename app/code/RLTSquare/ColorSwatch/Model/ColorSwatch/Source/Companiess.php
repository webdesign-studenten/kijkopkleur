<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Model\ColorSwatch\Source;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class Companiess
 * @package RLTSquare\ColorSwatch\Model\ColorSwatch\Source
 */
class Companiess extends AbstractSource
{
    /**
     * @var \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
     */
    private $modelColorSwatch;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * Companiess constructor.
     * @param \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $modelColorSwatch
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch $modelColorSwatch,
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->modelColorSwatch = $modelColorSwatch;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $brands = $this->modelColorSwatch->getAllBrands();
        $this->_options = [];
        $optionids = [];
        foreach ($brands as $brand) {
            $optionids[] = $brand['brand_id'];
        }
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'company_shades');
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if(in_array($option['value'],$optionids)) {
                $this->_options[] = ['label' => $option['label'], 'value' => $option['value']];
            }
        }
        return $this->_options;
    }
}
