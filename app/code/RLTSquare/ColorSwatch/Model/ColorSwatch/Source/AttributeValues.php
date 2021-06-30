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

use Magento\Framework\Option\ArrayInterface;

/**
 * Class AttributeValues
 * @package RLTSquare\ColorSwatch\Model\ColorSwatch\Source
 */
class AttributeValues implements ArrayInterface
{
    /**
     * @var
     */
    private $categoryHelper;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * AttributeValues constructor.
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    )
    {
        $this->eavConfig = $eavConfig;
    }
    /*
     * Return categories helper
     */
    /**
     * @param false $sorted
     * @param false $asCollection
     * @param bool $toLoad
     * @return mixed
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }
    /*
     * Option getter
     * @return array
     */
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }
    /*
     * Get options in "key-value" format
     * @return array
     */
    /**
     * @return array
     */
    public function toArray()
    {

        $attribute = $this->eavConfig->getAttribute('catalog_product', 'company_shades');
        $options = $attribute->getSource()->getAllOptions();
        $catagoryList = [];
        foreach ($options as $option) {
            if($option['value']) {
                $catagoryList[$option['value']] = __($option['label']);
            }

        }
        return $catagoryList;
    }
}
