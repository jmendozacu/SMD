<?php

class Epicor_Customerconnect_Block_Customer_Returns_Details_Info extends Epicor_Customerconnect_Block_Customer_Info
{

    public function _construct()
    {
        parent::_construct();
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */
        
        $this->_infoData = array(
            $this->__('Return Number :') => $return->getErpReturnsNumber() ? : $this->__('N/A'),
            $this->__('Customer Reference :') => $return->getCustomerReference() ? : $this->__('N/A'),
            $this->__('Created Date :') => $return->getRmaDate() ? $this->getHelper()->getLocalDate($return->getRmaDate(),
                    Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
            $this->__('Return Status :') => $return->getStatusDisplay(),
            $this->__('Customer Name :') => $return->getCustomerName(),
            $this->__('Credit Invoice Number :') => $return->getCreditInvoiceNumber() ? : $this->__('N/A'),
            $this->__('RMA Case Number :') => $return->getRmaCaseNumber() ? : $this->__('N/A'),
        );
        
        if($return->getErpSyncAction() != '') {
            $this->_infoData[$this->__('Erp Status :')] = $this->__('Awaiting Submission to ERP');
        }
        
        $this->setTitle($this->__('Return Information :'));
        $this->setColumnCount(2);
    }

}
