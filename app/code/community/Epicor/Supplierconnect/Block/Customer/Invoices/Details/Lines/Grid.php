<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Supplierconnect_Block_Customer_Invoices_Details_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_invoices_lines');
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

        $this->setDataSubset('invoice/lines/line');
        $this->setMessageType('suid');
        $this->setMessageBase('supplierconnect');

        $invoice = Mage::registry('supplier_connect_invoice_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $this->setCustomData((array)$invoice->getVarienDataArrayFromPath('invoice/lines/line'));
    }

    protected function _getColumns() {

        $columns = array(
            '_attributes_number' => array(
                'header' => Mage::helper('supplierconnect')->__('Line'),
                'align' => 'left',
                'index' => '_attributes_number',
                'type' => 'number',
                'filter' => false
            ),
            'supplier_product_code' => array(
                'header' => Mage::helper('supplierconnect')->__('Part Num'),
                'align' => 'left',
                'index' => 'supplier_product_code',
                'type' => 'text',
                'filter' => false,
                'keys' => array(
                    'product_code',
                    'supplier_product_code',
                ),
                'labels' => array(
                    'supplier_product_code' => 'Supplier'
                ),
                'join' => '<br />',
                'renderer' => new Epicor_Common_Block_Renderer_Composite()
            ),
            'description' => array(
                'header' => Mage::helper('supplierconnect')->__('Part Desc'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text',
                'filter' => false
            ),
            'supplier_quantity' => array(
                'header' => Mage::helper('supplierconnect')->__('Qty'),
                'align' => 'left',
                'index' => 'supplier_quantity',
                'type' => 'number',
                'filter' => false,
                'keys' => array(
                    'quantity',
                    'supplier_quantity',
                ),
                'labels' => array(
                    'supplier_quantity' => 'Supplier'
                ),
                'join' => '<br />',
                'renderer' => new Epicor_Common_Block_Renderer_Composite()
            ),
            'supplier_unit_of_measure_code' => array(
                'header' => Mage::helper('supplierconnect')->__('U/M'),
                'align' => 'left',
                'index' => 'supplier_unit_of_measure_code',
                'type' => 'text',
                'filter' => false,
                'keys' => array(
                    'unit_of_measure_code',
                    'supplier_unit_of_measure_code',
                ),
                'labels' => array(
                    'supplier_unit_of_measure_code' => 'Supplier'
                ),
                'join' => '<br />',
                'renderer' => new Epicor_Common_Block_Renderer_Composite()
            ),
            'price' => array(
                'header' => Mage::helper('supplierconnect')->__('Unit Cost'),
                'align' => 'left',
                'index' => 'price',
                'type' => 'number',
                'filter' => false,
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency()
            ),
            'per' => array(
                'header' => Mage::helper('supplierconnect')->__('Per'),
                'align' => 'left',
                'index' => 'per',
                'type' => 'text',
                'filter' => false,
            ),
            'line_value' => array(
                'header' => Mage::helper('supplierconnect')->__('Amount'),
                'align' => 'left',
                'index' => 'line_value',
                'type' => 'number',
                'filter' => false,
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency()
            ),
            'charges' => array(
                'header' => Mage::helper('supplierconnect')->__('Misc Charges'),
                'align' => 'left',
                'index' => 'charges',
                'type' => 'number',
                'filter' => false,
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency()
            ),
            'packing_slip' => array(
                'header' => Mage::helper('supplierconnect')->__('Packing'),
                'align' => 'left',
                'index' => 'packing_slip',
                'type' => 'text',
                'filter' => false,
                'keys' => array(
                    'packing_slip',
                    'pack_line',
                ),
                'labels' => array(
                    'packing_slip' => 'Slip',
                    'pack_line' => 'Line'
                ),
                'join' => '<br />',
                'renderer' => new Epicor_Common_Block_Renderer_Composite()
            )
        );

        return $columns;
    }

    public function getRowUrl($row) {
        return null;
    }

}