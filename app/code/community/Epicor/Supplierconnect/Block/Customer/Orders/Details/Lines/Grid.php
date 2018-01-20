<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_orders_lines');
        $this->setDefaultSort('_attributes_number');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $this->setIdColumn('_attributes_number');

        $this->setDataSubset('purchase_order/lines/line');
        $this->setMessageType('spod');
        $this->setMessageBase('supplierconnect');

        $order = Mage::registry('supplier_connect_order_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $this->setCustomData((array)$order->getVarienDataArrayFromPath('purchase_order/lines/line'));
    }

    protected function _getColumns() {

        $columns = array(
            'expand' => array(
                'header' => Mage::helper('supplierconnect')->__(''),
                'align' => 'left',
                'index' => 'expand',
                'type' => 'text',
                'column_css_class' => "expand-row",
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Expand()
            ),
            '_attributes_number' => array(
                'header' => Mage::helper('supplierconnect')->__('Line'),
                'align' => 'left',
                'index' => '_attributes_number',
                'type' => 'number',
            ),
            'product_code' => array(
                'header' => Mage::helper('supplierconnect')->__('Part Number'),
                'align' => 'left',
                'index' => 'product_code',
                'type' => 'text',
            ),
            'revision' => array(
                'header' => Mage::helper('supplierconnect')->__('Revision'),
                'align' => 'left',
                'index' => 'revision',
                'type' => 'text',
            ),
            'description' => array(
                'header' => Mage::helper('supplierconnect')->__('Description'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text',
            ),
            'quantity' => array(
                'header' => Mage::helper('supplierconnect')->__('Ordered Qty'),
                'align' => 'left',
                'index' => 'quantity',
                'type' => 'number',
            ),
            'unit_of_measure_code' => array(
                'header' => Mage::helper('supplierconnect')->__('UOM'),
                'align' => 'left',
                'index' => 'unit_of_measure_code',
                'type' => 'text',
            ),
            'price' => array(
                'header' => Mage::helper('supplierconnect')->__('Unit cost'),
                'align' => 'left',
                'index' => 'price',
                'type' => 'number',
            ),
            'line_value' => array(
                'header' => Mage::helper('supplierconnect')->__('Total Cost'),
                'align' => 'left',
                'index' => 'line_value',
                'type' => 'number',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Currency()
            ),
            'line_status' => array(
                'header' => Mage::helper('supplierconnect')->__('Status'),
                'align' => 'left',
                'index' => 'line_status',
                'type' => 'text',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Status()
            ),
            'comment' => array(
                'header' => Mage::helper('supplierconnect')->__('Line Comments'),
                'align' => 'left',
                'index' => 'comment',
                'type' => 'text',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Linecomments()
            ),
            'releases' => array(
                'header' => Mage::helper('epicor_comm')->__(''),
                'align' => 'left',
                'index' => 'releases',
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Details_Lines_Renderer_Linereleases(),
                'type' => 'text',
                'filter' => false,
                'column_css_class' => "expand-content",
                'header_css_class' => "expand-content",
                'keep_data_format' => 1 // prevents data from this index being flattened
            ),
        );

        return $columns;
    }

    public function getRowUrl($row) {
        return null;
    }

}