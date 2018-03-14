<?php

/**
 * Customer RMA list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rmas_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_rmas');
        $this->setDefaultSort('returns_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('curs');
        $this->setIdColumn('returns_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text'=>'CSV', 'url'=>'*/*/exportRmasCsv'));
        $this->setExportTypeXml(array('text'=>'XML', 'url'=>'*/*/exportRmasXml'));
    }

    public function getRowUrl($row) {
        return null;
    }

}