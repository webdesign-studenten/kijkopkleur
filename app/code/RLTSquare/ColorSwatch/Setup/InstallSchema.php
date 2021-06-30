<?php
/**
 *
 * @category : RLTSquare
 * @Package  : RLTSquare_ColorSwatch
 * @Author   : RLTSquare <support@rltsquare.com>
 * @copyright Copyright 2021 © rltsquare.com All right reserved
 * @license https://rltsquare.com/
 */

namespace RLTSquare\ColorSwatch\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package RLTSquare\ColorSwatch\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable('rlt_square_colorswatch'))
            ->addColumn(
                'colorswatch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'colorswatch_id'
            )
            ->addColumn(
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                ['nullable' => true, 'default' => 0],
                'brand_id'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => '1'],
                'status'
            )
            ->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' =>  \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            );
        $installer->getConnection()->createTable($table);
        $table = $installer->getConnection()
            ->newTable($installer->getTable('rltsquare_colorswatch_products'))
            ->addColumn(
                'product_related_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Related Id'
            )
            ->addColumn(
                'colorswatch_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'colorswatch_id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'Product Id'
            )
            ->setComment('rltsquare_colorswatch_products');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
