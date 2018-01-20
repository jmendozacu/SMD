<?php

/**
 * Parts Price breaks grid config
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Breaks_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setId('supplierconnect_parts_breaks');
        $this->setDefaultSort('quantity');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setMessageBase('supplierconnect');
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $part = Mage::registry('supplier_connect_part_details');
        /* @var $part Epicor_Common_Model_Xmlvarien */
        $breaks = $part->getVarienDataArrayFromPath('part/price_breaks/price_break');
        $this->setCustomData((array)$breaks);
    }

    protected function _getColumns() {

        $columns = array(
            'quantity' => array(
                'header' => Mage::helper('epicor_comm')->__('Minimum Quantity'),
                'align' => 'left',
                'index' => 'quantity',
                'type' => 'number',
                'filter' => false,
                'width' => '33%'
            ),
            'modifier' => array(
                'header' => Mage::helper('epicor_comm')->__('Modifier'),
                'align' => 'left',
                'index' => 'modifier',
                'type' => 'text',
                'filter' => false,
                'width' => '33%'
            ),
            'effective_price' => array(
                'header' => Mage::helper('epicor_comm')->__('Effective Price'),
                'align' => 'left',
                'index' => 'effective_price',
                'type' => 'number',
                'filter' => false,
                'width' => '33%'
            ),
        );

        return $columns;
    }

    public function getRowUrl($row) {
        return null;
    }

}