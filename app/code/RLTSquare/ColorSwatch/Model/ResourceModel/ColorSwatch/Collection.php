<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch;
use \RLTSquare\ColorSwatch\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'colorswatch_id';
    /**
     * @var
     */
    protected $_previewFlag;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('RLTSquare\ColorSwatch\Model\ColorSwatch', 'RLTSquare\ColorSwatch\Model\ResourceModel\ColorSwatch');
        $this->_map['fields']['colorswatch_id'] = 'main_table.colorswatch_id';
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function addPriorityFilter($dir = 'ASC')
    {
        $this->getSelect()
            ->order('main_table.sort_order ' . $dir);
        return $this;
    }
}
