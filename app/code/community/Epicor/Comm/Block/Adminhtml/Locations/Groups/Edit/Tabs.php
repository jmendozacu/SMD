<?php

class Epicor_Comm_Block_Adminhtml_Locations_Groups_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Group');
    }

    protected function _beforeToHtml()
    {
        
        $this->addTab('form_groups', array(
            'label' => Mage::helper('epicor_comm')->__('General'),
            'title' => Mage::helper('epicor_comm')->__('General'),
            'content'   => $this->getLayout()->createBlock('epicor_comm/adminhtml_locations_groups_edit_tab_form')->toHtml(),
        ));
        
        $this->addTab('form_groups_locations', array(
            'label' => Mage::helper('epicor_comm')->__('Locations'),
            'title' => Mage::helper('epicor_comm')->__('Locations'),
            'url'       => $this->getUrl('*/*/locations', array('_current' => true)),
            'class' => 'ajax'
        ));

        return parent::_beforeToHtml();
    }

}
