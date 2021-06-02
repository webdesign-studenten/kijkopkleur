<?php
/**
 * Admin can sync customer, products, sales, cart, newsletter subscribers, wishlist etc.
 * Copyright (C) 2019  
 * 
 * This file is part of WebdesignStudenten/EasySync.
 * 
 * WebdesignStudenten/EasySync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace WebdesignStudenten\EasySync\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //Your install script

        $table_webdesignstudenten_easysync_dataid = $setup->getConnection()->newTable($setup->getTable('webdesignstudenten_easysync_data_sync'));

        $table_webdesignstudenten_easysync_dataid->addColumn(
            'data_sync_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_webdesignstudenten_easysync_dataid->addColumn(
            'dataID',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'dataID'
        );
        $table_webdesignstudenten_easysync_dataid->addColumn(
            'ServerLocation',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'ServerLocation'
        );


        $table_webdesignstudenten_easysync_dataid->addColumn(
            'dataScope',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'dataScope'
        );

        $table_webdesignstudenten_easysync_dataid->addColumn(
            'UpdateDate',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => False],
            'UpdateDate'
        );

        $table_webdesignstudenten_easysync_dataid->addColumn(
            'LogType',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => False],
            'LogType'
        );
        
        $table_webdesignstudenten_easysync_dataid->addColumn(
            'ChangeLog',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => False],
            'ChangeLog'
        );

        
        $table_webdesignstudenten_easysync_dataid->addColumn(
            'OldValue',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => False],
            'OldValue'
        );

        $table_webdesignstudenten_easysync_dataid->addColumn(
            'UpdateFlag',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => False],
            'UpdateFlag'
        );

        $setup->getConnection()->createTable($table_webdesignstudenten_easysync_dataid);
    }
}
