<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Common_Block_Adminhtml_Access_Right_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Initialize Tabs
     *
     */
    // protected $_attributeTabBlock = 'epicor_common/block_adminhtml_access_right_edit_tab_details';

    public function __construct() {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Access Right');
    }

    protected function _beforeToHtml() {
        $block = $this->getLayout()->createBlock('epicor_common/adminhtml_access_right_edit_tab_details');
        $right = Mage::registry('access_right_data');
        
        $this->addTab('form_details', array(
            'label' => 'General',
            'title' => 'General Information',
            'content' => $block->toHtml(),
        ));
        
        $this->addTab('form_element', array(
            'label' => 'Elements',
            'title' => 'Elements',
            'url' => $this->getUrl('*/*/elements', array('id' => $right->getId(), '_current' => true)),
            'class' => 'ajax',
        ));

        $this->addTab('form_groups', array(
            'label' => 'Groups',
            'title' => 'Groups',
            'url' => $this->getUrl('*/*/groups', array('id' => $right->getId(), '_current' => true)),
            'class' => 'ajax',
        ));

        return parent::_beforeToHtml();
    }

}
