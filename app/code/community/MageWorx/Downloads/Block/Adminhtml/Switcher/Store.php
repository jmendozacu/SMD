<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Switcher_Store extends Mage_Adminhtml_Block_Store_Switcher
{
    protected $_storeIds;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageworx/downloads/switcher_store.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName(Mage::helper('mageworx_downloads')->__('All Store Views'));
    }
}