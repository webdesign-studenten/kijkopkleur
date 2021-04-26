<?php
namespace Packs\Magento2\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema implements  InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        try {
            $tableShipment = $setup->getConnection()->newTable(
                $setup->getTable('packs_magento2_shipment')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                10,
                array(
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ),
                'Entity Id'
            )->addColumn(
                'magento_order_id',
                Table::TYPE_INTEGER,
                10,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                ),
                'Magento Order Id'
            )->addColumn(
                'magento_shipment_id',
                Table::TYPE_INTEGER,
                10,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                ),
                'Magento Shipment Id'
            )->addColumn(
                'packs_shipment_id',
                Table::TYPE_INTEGER,
                10,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                ),
                'Packs Shipment Id'
            )->addColumn(
                'packs_shipment_item_ids',
                Table::TYPE_TEXT,
                1000,
                array(
                    'unsigned' => true,
                    'nullable' => true,
                ),
                'Packs Shipment Item Ids'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                array(
                    'nullable' => false,
                ),
                'Created At'
            )->addColumn(
                'load_date',
                Table::TYPE_TIMESTAMP,
                null,
                array(
                    'nullable' => true,
                ),
                'Updated At'
            )->addColumn(
                'delivery_date',
                Table::TYPE_TIMESTAMP,
                null,
                array(
                    'nullable' => true,
                ),
                'Confirmed At'
            )->addColumn(
                'confirm_date',
                Table::TYPE_TIMESTAMP,
                null,
                array(
                    'nullable' => false,
                ),
                'Confirm Date'
            )->addColumn(
                'confirm_status',
                Table::TYPE_SMALLINT,
                32, array(
                    'default' => '0'
            ),
                'Confirm Status'
            )->addColumn(
                'shipment_type',
                Table::TYPE_TEXT,
                32, array(
                    'nullable' => true,
            ), 'Shipment Type'
            )->addColumn(
                'seal_type',
                Table::TYPE_TEXT,
                32, array(
                    'nullable' => true,
            ), 'Seal Type'
            )->addColumn(
                'colli',
                Table::TYPE_TEXT,
                32,
                array(
                    'nullable' => true,
                ),
                'Colli'
            )->addColumn(
                'weight',
                Table::TYPE_TEXT,
                32, array(
                    'nullable' => true,
            ),
                'Weight'
            )->addColumn(
                'allowance',
                Table::TYPE_TEXT,
                32, array(
                    'nullable' => true,
            ),
                'Allowance'
            )->addColumn(
                'reference',
                Table::TYPE_TEXT,
                32,
                array(
                    'nullable' => true,
                ), 'Reference'
            )->addIndex(
                $setup->getIdxName(
                    'packs_magento2_shipment',
                    array(
                        'packs_shipment_id'
                    ),
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                array(
                    'magento_shipment_id'
                ),
                array(
                    'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                )
            )->setComment(
                'Packs Shipment'
            );
        } catch (\Zend_Db_Exception $e) {
        }
        $setup->getConnection()->createTable($tableShipment);

        $setup->endSetup();
    }
}