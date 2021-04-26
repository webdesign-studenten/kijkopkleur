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
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
   public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();
		/**
		 * Creating table magentostudy_news
		 */
		$table = $installer->getConnection()->newTable(
		    $installer->getTable('vatexempt_medicalcondition')
		)->addColumn(
		    'condition_id',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		    'Medical Condition ID'
		)->addColumn(
		    'condition_name',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		    255,
		    ['nullable' => true,'default' => null],
		    'Condition Name'
		)->addColumn(
		    'status',
		    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
		    '2M',
		    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
		    'Status'
		)->addColumn(
		    'created_at',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		    null,
		    ['nullable' => false],
		    'Created At'
		)->addColumn(
		    'published_at',
		    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		    null,
		    ['nullable' => true,'default' => null],
		    'Publish date'
		)->addIndex(
		    $installer->getIdxName(
		        'vatexempt_medicalcondition',
		        ['published_at'],
		        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
		    ),
		    ['published_at'],
		    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
		)->setComment(
		    'Vatexempt Medical Conditions'
		);
		
		$installer->getConnection()->createTable($table);
		
		
		$table = $installer->getConnection()->newTable(
		    $installer->getTable('vatexempt_details')
		)->addColumn(
		    'detail_id',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
		    'Detail ID'
		)->addColumn(
		    'product_id',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    ['unsigned' => true, 'default' => null],
		    'Product ID'
		)->addColumn(
		    'order_id',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    ['unsigned' => true, 'default' => null],
		    'Order ID'
		)->addColumn(
		    'condition_id',
		    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
		    null,
		    ['unsigned' => true, 'default' => null],
		    'Medical Condition ID'
		)->addColumn(
		    'product_name',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		    255,
		    ['nullable' => true,'default' => null],
		    'Product Name'
		)->addColumn(
		    'product_sku',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		    255,
		    ['nullable' => true,'default' => null],
		    'Product SKU'
		)->addColumn(
		    'applicant_name',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
		    255,
		    ['nullable' => true,'default' => null],
		    'Applicant Name'
		)->addColumn(
		    'created_at',
		    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
		    null,
		    ['nullable' => false],
		    'Created At'
		)->addColumn(
		    'published_at',
		    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
		    null,
		    ['nullable' => true,'default' => null],
		    'Publish date'
		)->addIndex(
		    $installer->getIdxName(
		        'vatexempt_details',
		        ['published_at'],
		        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
		    ),
		    ['published_at'],
		    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
		)->setComment(
		    'Vatexempt Detials'
		);
		
		$installer->getConnection()->createTable($table);
		
		$installer->endSetup();
	}
}