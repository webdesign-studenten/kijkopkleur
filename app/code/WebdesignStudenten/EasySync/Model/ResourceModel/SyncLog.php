<?php
/**
 * Copyright © 2015 WebdesignStudenten. All rights reserved.
 */
namespace WebdesignStudenten\EasySync\Model\ResourceModel;

/**
 * SyncLog resource
 */
class SyncLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('webdesignstudenten_easysync_data_sync', 'data_sync_id');
    }

  
}
