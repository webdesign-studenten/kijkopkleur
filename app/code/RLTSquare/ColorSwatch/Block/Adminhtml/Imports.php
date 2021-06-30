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

use Magento\Framework\UrlInterface;

/**
 * Class Imports
 * @package RLTSquare\ColorSwatch\Block\Adminhtml
 */
class Imports extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'import/imports.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;
    /**
     * @var \Magento\Backend\Block\Widget\Grid\Export
     */
    protected $Export;
    /**
     * @var
     */
    protected $store;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(

        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\Store $store,
        \Magento\Backend\Block\Widget\Grid\Export $Export,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->Export = $Export;
        $this->_store = $store;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getExportTypes()
    {
        $array = array(
            0 => array(
                'label' => 'CSV',
                'value' => 'csv'
            ),
            1 => array(
                'label' => 'Excel',
                'value' => 'excel'
            ),
        );

        return $array;

    }

    /**
     * @return string
     */
    public function getSampleDownloadUrl()
    {

        return $this->_store->getBaseUrl((UrlInterface::URL_TYPE_MEDIA)).'SamplePromotedProduct/productpartfinder_promoted_products_sample.csv';

    }

}
