<?php

/**
 * Customer Orders list
 */
class Epicor_Customerconnect_Block_Customer_Account_Contacts_List extends Epicor_Common_Block_Generic_List {

    protected function _setupGrid() {
        $this->_controller = 'customer_account_contacts_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('Contacts');

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        
        $details = Mage::registry('customer_connect_account_details');

        if ($details) {
            if ($helper->customerHasAccess('Epicor_Customerconnect', 'Account', 'saveContact', '', 'Access')) {
                 if(Mage::helper('customerconnect')->checkMsgAvailable('CUAU')){
                    $this->_addButton('Add Contact', array(
                        'id' => 'add-contact',
                        'label' => Mage::helper('customerconnect')->__('New Contact'),
                        'class' => 'add',
                            ), -100);
                 }    
            }
        }
    }

    protected function _postSetup() {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}