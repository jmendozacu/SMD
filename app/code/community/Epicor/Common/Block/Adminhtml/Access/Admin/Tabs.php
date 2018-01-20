<?php

class Epicor_Common_Block_Adminhtml_Access_Admin_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Initialize Tabs
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('admin_form'); // this should be same as the form id define above
        $this->setTitle('Access Management Administration');
    }

    protected function _beforeToHtml() {
        $this->addTab('form_element', array(
            'label' => 'Excluded Elements',
            'title' => 'Excluded Elements',
            'url' => $this->getUrl('*/*/excludedelements', array('_current' => true)),
            'class' => 'ajax',
        ));

        return parent::_beforeToHtml();
    }

}
