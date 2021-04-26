<?php

/**

 * Copyright © 2015 Magento. All rights reserved.

 * See COPYING.txt for license details.

 */

namespace Milople\Vatexempt\Api;



/**

 * Interface for managing customer shipping address information

 * @api

 */

interface VatexemptInformationManagementInterface

{

     /**

     * Save Vat Exempt Information

     *

     * @param mixed  $vatexempt 

     * @return 

     */

    public function saveVatexemptInformation($vatexempt);

}

