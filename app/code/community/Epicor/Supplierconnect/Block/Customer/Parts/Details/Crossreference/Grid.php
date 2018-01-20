<?php

/**
 * Parts crossreference grid config
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Crossreference_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        $this->setId('supplierconnect_parts_crossreference');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $this->setMessageBase('supplierconnect');

        $part = Mage::registry('supplier_connect_part_details');
        /* @var $part Epicor_Common_Model_Xmlvarien */
        $crossReferences = $part->getVarienDataArrayFromPath('part/cross_reference_parts/cross_reference_part');
        $this->setCustomData((array)$crossReferences);
    }

    protected function _getColumns() {

        $columns = array(
            'manufacturer_name' => array(
                'header' => Mage::helper('epicor_comm')->__('Qualified Manufacturer'),
                'align' => 'left',
                'index' => 'manufacturer_name',
                'type' => 'text',
                'filter' => false
            ),
            'manufacturers_product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Manufacturer\'s Part'),
                'align' => 'left',
                'index' => 'manufacturers_product_code',
                'type' => 'text',
                'filter' => false
            ),
            'supplier_product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Supplier Part'),
                'align' => 'left',
                'index' => 'supplier_product_code',
                'type' => 'text',
                'filter' => false
            ),
            'supplier_lead_days' => array(
                'header' => Mage::helper('epicor_comm')->__('Supplier\'s Lead Days'),
                'align' => 'left',
                'index' => 'supplier_lead_days',
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