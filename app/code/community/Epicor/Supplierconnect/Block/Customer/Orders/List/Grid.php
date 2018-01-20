<?php

/**
 * Supplier Purchase orders list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_orders_list');
        $this->setDefaultSort('purchase_order_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('spos');
        $this->setIdColumn('purchase_order_number');
        $this->initColumns();

        $filter = new Varien_Object(array(
            'field' => 'confirm_via',
            'value' => array('neq' => 'NEW'),
        ));

        $this->setAdditionalFilters(array($filter));
    }

    public function getRowUrl($row) {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Supplierconnect', 'Orders', 'details', '', 'Access')) {
            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            $erp_account_number = $helper->getSupplierAccountNumber();
            $requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getId()));
            $url = Mage::getUrl('*/*/details', array('order' => $requested));
        }

        return $url;
    }

}