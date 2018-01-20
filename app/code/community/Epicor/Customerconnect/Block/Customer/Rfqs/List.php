<?php

/**
 * Customer RFQ list
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_List extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_rfqs_list';
        $this->_blockGroup = 'customerconnect';
        $this->_headerText = Mage::helper('customerconnect')->__('RFQs');

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $msgHelper = Mage::helper('epicor_comm/messaging');
        /* @var $msgHelper Epicor_Comm_Helper_Messaging */
        $enabled = $msgHelper->isMessageEnabled('customerconnect', 'crqu');
        
        $erpAccount = $msgHelper->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        $currencyCode = $erpAccount->getCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
        
        if ($enabled && $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'new', '', 'Access') && $currencyCode) {
            $url = Mage::getUrl('*/*/new/');
            $this->_addButton(
                'new',
                array(
                'label' => Mage::helper('customerconnect')->__('New Quote'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'add',
                ), 10
            );
        }
    }

}
