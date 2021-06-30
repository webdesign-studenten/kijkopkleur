<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Model;

/**
 * Class ColorSwatch
 * @package RLTSquare\ColorSwatch\Model
 */
class ColorSwatch extends \Magento\Framework\Model\AbstractModel
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch');
    }

    /**
     * @return string[]
     */
    public function getAvailableStatuses()
    {
        $availableOptions = ['1' => 'Enable',
            '0' => 'Disable'];
        return $availableOptions;
    }

    /**
     * @param $object
     * @return mixed
     */
    public function getProducts($object)
    {
        $tbl = $this->getResource()->getTable("rltsquare_colorswatch_products");
        $select = $this->getResource()->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
            ->where(
                'colorswatch_id = ?',
                (int)$object->getId()
            );
        return $this->getResource()->getConnection()->fetchCol($select);
    }
}
