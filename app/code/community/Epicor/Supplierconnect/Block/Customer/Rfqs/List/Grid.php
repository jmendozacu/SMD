<?php

/**
 * Supplier Rfqs list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Rfqs_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_rfqs');
        $this->setDefaultSort('rfq_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('surs');
        $this->setIdColumn('rfq_number');
        $this->setDefaultFilter(array('status' => 'O'));
        $this->initColumns();
    }

    public function getRowUrl($row) {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Supplierconnect', 'Rfq', 'details', '', 'Access')) {
            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            $erp_account_number = $helper->getSupplierAccountNumber();
            $rfq_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getId() . ']:[' . $row->getLine()));
            $url = Mage::getUrl('*/*/details', array('rfq' => $rfq_requested));
        }

        return $url;
    }

}