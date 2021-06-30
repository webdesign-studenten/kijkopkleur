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
 * Class Status
 */
class Status implements ArrayInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = ['1' => 'Enable', '0' => 'Disable'];
        $options = [];
        foreach ($availableOptions as $key => $label) {
            $options[] = [
                'label' => $label,
                'value' => $key,
            ];
        }
        return $options;
    }
}
