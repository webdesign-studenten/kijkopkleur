<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PDFCustom
 */


namespace Amasty\PDFCustom\Controller\Guest;

class Order extends \Amasty\PDFCustom\Controller\Sales\Order
{
    protected function getRedirect()
    {
        return $this->_redirect('sales/guest/form');
    }
}
