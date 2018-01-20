<?php

/**
 * Supplier Payments list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Payments_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_payments');
        $this->setDefaultSort('payment_date');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('sups');
        $this->setIdColumn('invoice_number');
        $this->initColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

}