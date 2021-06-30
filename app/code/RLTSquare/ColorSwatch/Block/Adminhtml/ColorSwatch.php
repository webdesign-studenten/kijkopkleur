<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */
namespace RLTSquare\ColorSwatch\Block\Adminhtml;

/**
 * Class ColorSwatch
 * @package RLTSquare\ColorSwatch\Block\Adminhtml
 */
class ColorSwatch extends \Magento\Backend\Block\Template
{

    /**
     * @var string
     */
    protected $_template = 'colorswatch.phtml';

    /**
     * ColorSwatch constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

}
