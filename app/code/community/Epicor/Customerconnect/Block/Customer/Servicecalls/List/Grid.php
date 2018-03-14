<?php

/**
 * Customer Service Call list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Servicecalls_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_servicecalls');
        $this->setDefaultSort('call_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cucs');
        $this->setIdColumn('call_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text'=>'CSV', 'url'=>'*/*/exportServicecallsCsv'));
        $this->setExportTypeXml(array('text'=>'XML', 'url'=>'*/*/exportServicecallsXml'));
    }

    public function getRowUrl($row) {
        return null;
    }

}