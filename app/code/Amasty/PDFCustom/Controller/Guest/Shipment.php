<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Controller\Guest;

class Shipment extends \Amasty\PDFCustom\Controller\Sales\Shipment
{
    protected function getRedirect()
    {
        return $this->_redirect('sales/guest/form');
    }
}
