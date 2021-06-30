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
 * Class Colors
 * @package RLTSquare\ColorSwatch\Model\ColorSwatch\Source
 */
class Colors extends AbstractSource
{

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [];
        $this->_options[] = ['label' => 'Shade', 'value' => 'shade'];
        $this->_options[] = ['label' => 'White', 'value' => 'white'];
        $this->_options[] = ['label' => 'Light', 'value' => 'light'];
        $this->_options[] = ['label' => 'Dark', 'value' => 'dark'];
        $this->_options[] = ['label' => '__', 'value' => 'empty'];
        return $this->_options;
    }
}
