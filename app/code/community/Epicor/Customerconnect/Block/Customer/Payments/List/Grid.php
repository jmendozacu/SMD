<?php

/**
 * Customer Payments list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Payments_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('customerconnect_payments');
        $this->setDefaultSort('payment_date');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cups');
        $this->setIdColumn('payment_reference');
        $this->initColumns();
        $this->setExportTypeCsv(array('text'=>'CSV', 'url'=>'*/*/exportPaymentsCsv'));
        $this->setExportTypeXml(array('text'=>'XML', 'url'=>'*/*/exportPaymentsXml'));
    }

    public function getRowUrl($row) {
        return false;
    }

}