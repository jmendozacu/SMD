<?php

/**
 * Customer Recent Purchase list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Recentpurchases_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();
        $toTime = strtotime("-210 hour");
        $toDate = date("m/d/Y", $toTime);
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $this->setId('customerconnect_rph');
        $this->setDefaultSort('last_ordered_date');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cphs');
        $this->setIdColumn('product_code');
        $this->setDefaultFilter(array('last_ordered_date' => array('from' => $toDate, 'locale' => $locale)));
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportOrdersCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportOrdersXml'));
    }

    public function getRowUrl($row) {
        return false;
    }

}
