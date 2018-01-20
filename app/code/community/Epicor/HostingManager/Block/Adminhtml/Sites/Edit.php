<?php

/**
 * Sites edit block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Sites_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{ 

    public function __construct()
    {
        $this->_controller = 'adminhtml_sites';
        $this->_blockGroup = 'hostingmanager';
        $this->_mode = 'edit';

        parent::__construct();
        
        $this->removeButton('reset');
        
        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Reset'),
            'onclick'   => 'setLocation(\'' . $this->getResetUrl() . '\')',
        ), -1);
    }

    public function getResetUrl()
    {
        return $this->getUrl('*/*/edit', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    public function getHeaderText()
    {
        if (Mage::registry('current_site')->getId()) {
            return $this->htmlEscape(Mage::registry('current_site')->getName());
        } else {
            return Mage::helper('hostingmanager')->__('New Site');
        }
    }

}
