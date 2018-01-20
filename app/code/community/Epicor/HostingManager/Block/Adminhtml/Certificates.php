<?php

/**
 * Certificates grid container block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'hostingmanager';
        $this->_controller = 'adminhtml_certificates';
        $this->_headerText = Mage::helper('hostingmanager')->__('Certificates');
        $this->_addButtonLabel = Mage::helper('hostingmanager')->__('Add Certificate');
        parent::__construct();
    }

}
