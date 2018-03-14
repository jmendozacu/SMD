<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Resource_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mageworx_downloads/customer', 'id');
    }
}
