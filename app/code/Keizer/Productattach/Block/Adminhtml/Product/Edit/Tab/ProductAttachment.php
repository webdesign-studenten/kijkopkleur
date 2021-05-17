<?php

namespace Keizer\Productattach\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Class ProductAttachment
 * @package Keizer\Productattach\Block\Adminhtml\Product\Edit\Tab
 */
class ProductAttachment extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'catalog/product/edit/attachment.phtml';

    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
}