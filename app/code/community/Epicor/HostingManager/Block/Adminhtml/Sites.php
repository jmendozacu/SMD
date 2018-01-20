<?php

/**
 * Sites grid container
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Sites extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'hostingmanager';
        $this->_controller = 'adminhtml_sites';
        $this->_headerText = Mage::helper('hostingmanager')->__('Sites');
        $this->_addButtonLabel = Mage::helper('hostingmanager')->__('Add Site');
        parent::__construct();
    }

}
