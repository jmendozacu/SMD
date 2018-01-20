<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Lists_Block_Customer_Account_Contracts_Parts_Uom_Grid extends Epicor_Common_Block_Generic_List_Grid {

    
    public function __construct() {
        parent::__construct();      
        
        $this->setId('customer_contracts_parts_uom');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('epicor_lists');
        $this->setCustomColumns($this->_getColumns());
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);

        $details = Mage::registry('contracts_parts_row');   // as this is a sub grid, this is set in the uom renderer and is an array of varien objects
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */
        if($details) {
            $this->setCustomData($details);

        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());            
            $this->setFilterVisibility(false);
            $this->setPagerVisibility(false);
        }
        
    }

    protected function _getColumns() {
        $columns = array(
            'unit_of_measure_code' => array(
                'header' => Mage::helper('epicor_lists')->__('Uom Code'),
                'align' => 'left',
                'index' => 'unit_of_measure_code',
                'width' => '50px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'minimum_order_qty' => array(
                'header' => Mage::helper('epicor_lists')->__('Min Order Qty'),
                'align' => 'left',
                'index' => 'minimum_order_qty',
                'width' => '50px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'maximum_order_qty' => array(
                'header' => Mage::helper('epicor_lists')->__('Max Order Qty'),
                'align' => 'left',
                'index' => 'maximum_order_qty',
                'width' => '50px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'contract_qty' => array(
                'header' => Mage::helper('epicor_lists')->__('Contract Qty'),
                'align' => 'left',
                'index' => 'contract_qty',
                'width' => '50px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'is_discountable' => array(
                'header' => Mage::helper('epicor_lists')->__('Is Discountable'),
                'align' => 'left',
                'index' => 'is_discountable',
                'width' => '10px',
                'type' => 'text',
                'condition' => 'LIKE'
            ),                
            'currencies' => array(
                'header' => Mage::helper('epicor_lists')->__('Price'),
                'align' => 'left',
                'index' => 'currencies',
                'width' => '50px',
                'type' => 'text',
                'renderer' => new Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Currencies(),
                'condition' => 'LIKE'
            ),                
            
        );
        
        return $columns;
    }
    
    public function getRowUrl($row) {
        return false;
    }
    
}
