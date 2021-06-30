<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Block\Adminhtml\ColorSwatch;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class SaveAndContinueButton
 */
class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
