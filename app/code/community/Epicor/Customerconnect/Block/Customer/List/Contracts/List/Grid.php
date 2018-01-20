<?php

/**
 * Customer Orders list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_List_Contracts_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('customerconnect_customer_list_contracts_list_grid');
        $this->setDefaultSort('account_number');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cccs');
        $this->setIdColumn('account_number');
        $this->setSaveParametersInSession(false);
        
        $this->initColumns();
    }

    public function getRowUrl($row) {
        
        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Customerconnect', 'Contract', 'details', '', 'Access')) {
            $helper = Mage::helper('epicor_lists');
            /* @var $helper Epicor_Lists_Helper_Data */
            $erp_account_number = $helper->getErpAccountNumber();
            $contract = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getContractCode()));
         //   $contract = $helper->urlEncode($helper->encrypt($row->getContractCode()));
            $params = array('contract' => $contract);
            $url = Mage::getUrl('*/*/details', $params);
//            $url = Mage::getUrl('epicor_lists/contract/details', $params);
            
         
//            $erp_account_number = $helper->getErpAccountNumber();
//            $order_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getId()));
//            $url = Mage::getUrl('*/*/details', array('order' => $order_requested));
        }

        return $url;
        
    }

    protected function initColumns() {
        parent::initColumns();

        $columns = $this->getCustomColumns();

        $this->setCustomColumns($columns);
    } 
}