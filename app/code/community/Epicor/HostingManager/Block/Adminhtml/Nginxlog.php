<?php

/**
 * Nginx log grid container block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Nginxlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'hostingmanager';
        $this->_controller = 'adminhtml_nginxlog';
        $this->_headerText = Mage::helper('hostingmanager')->__('Nginx log');
        parent::__construct();
        $this->removeButton('add');
    }

}
