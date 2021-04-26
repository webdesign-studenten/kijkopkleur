<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Setup\Operation;

use Amasty\PDFCustom\Model\ResourceModel\Template as TemplateResource;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class ChangeCssIncludeInTemplates
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $table = $setup->getTable(TemplateResource::MAIN_TABLE);
        $replace = 'REPLACE(template_text, "Amasty_PDFCustom/css/ampdf.css", "Amasty_PDFCustom::css/ampdf.css")';
        $connection->update(
            $table,
            [
                'template_text' => new \Zend_Db_Expr($replace)
            ]
        );
    }
}
