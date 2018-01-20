<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Erp Account');
    }

    protected function _beforeToHtml()
    {
        $detailsBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_new_tab_erpinfo');

        $this->addTab('form_details', array(
            'label' => 'Details',
            'title' => 'Details',
            'content' => $detailsBlock->toHtml(),
        ));

        $addressBlock = $this->getLayout()->createBlock('epicor_comm/adminhtml_customer_erpaccount_new_tab_address');

        $this->addTab('form_address', array(
            'label' => 'Addresses',
            'title' => 'Addresses',
            'content' => $addressBlock->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
