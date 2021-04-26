<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_VATExempt
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento-extensions/vat-exempt-m2.html
*
**/
namespace Milople\Vatexempt\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {
    /**
     * EAV setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {

        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
       /** 
          @var
          EavSetup
          $eavSetup 
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'vatstatus', [
            'group'=> 'VAT Exempt',
            'type'=>'int',
            'backend'=>'',
            'frontend'=>'',
            'label'=>'VAT Exempt',
            'input'=>'boolean',
            'class'=>'',
            'source'=>'',
            'global'=>\Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible'=>true,
            'required'=>false,
            'user_defined'=>true,
            'default'=>'0',
            'searchable'=>false,
            'filterable'=>false,
            'comparable'=>false,
            'visible_on_front'=>false,
            'used_in_product_listing'=>true,
            'unique'=>false,
            'apply_to'=>'simple,configurable,virtual,downloadable'
           ]);
    }

}

