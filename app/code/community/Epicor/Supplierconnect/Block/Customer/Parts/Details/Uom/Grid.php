<?php

/**
 * Parts UOM grid config
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Uom_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        $this->setId('supplierconnect_parts_uom');
        $this->setDefaultSort('unit_of_measure');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $this->setMessageBase('supplierconnect');
        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $part = Mage::registry('supplier_connect_part_details');
        /* @var $part Epicor_Common_Model_Xmlvarien */
        $breaks = $part->getVarienDataArrayFromPath('part/suppliers_unit_of_measures/supplier_unit_of_measure');
        $this->setCustomData((array)$breaks);
    }

    protected function _getColumns() {

        $columns = array(
            'unit_of_measure' => array(
                'header' => Mage::helper('epicor_comm')->__('Unit Of Measure'),
                'align' => 'left',
                'index' => 'unit_of_measure',
                'type' => 'text',
                'filter' => false
            ),
            'conversion_factor' => array(
                'header' => Mage::helper('epicor_comm')->__('Conversion Factor'),
                'align' => 'left',
                'index' => 'conversion_factor',
                'type' => 'text',
                'filter' => false
            ),
            'operator' => array(
                'header' => Mage::helper('epicor_comm')->__('Operator'),
                'align' => 'left',
                'index' => 'operator',
                'type' => 'text',
                'filter' => false
            ),
            'value' => array(
                'header' => Mage::helper('epicor_comm')->__('Value'),
                'align' => 'left',
                'index' => 'value',
                'type' => 'number',
                'filter' => false
            ),
            'result' => array(
                'header' => Mage::helper('epicor_comm')->__('Result'),
                'align' => 'left',
                'index' => 'result',
                'type' => 'text',
                'filter' => false
            ),
        );

        return $columns;
    }

    public function getRowUrl($row) {
        return null;
    }

}