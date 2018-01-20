<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customerconnect_order_parts');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('customerconnect');
        $this->setMessageType('cuod');
        $this->setIdColumn('product_code');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $order = Mage::registry('customer_connect_order_details');
        /* @var $order Epicor_Common_Model_Xmlvarien */
        $lines = ($order->getLines()) ? $order->getLines()->getasarrayLine() : array();

        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line->setQty($line->getQuantity() ? $line->getQuantity()->getOrdered() : 0);
            }
        }

        $this->setCustomData((array) $lines);
    }

    protected function _getColumns()
    {

        $columns = array(
            'expand' => array(
                'header' => Mage::helper('epicor_comm')->__(''),
                'align' => 'left',
                'index' => 'expand',
                'type' => 'text',
                'column_css_class' => "expand-row",
                'renderer' => new Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Expand(),
                'filter' => false
            ),
            'is_kit' => array(
                'header' => Mage::helper('epicor_comm')->__('Kit'),
                'align' => 'left',
                'index' => 'is_kit',
                'type' => 'text',
                'filter' => false
            ),
            'product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Part Number'),
                'align' => 'left',
                'index' => 'product_code',
                'filter' => false
            ),
            'description' => array(
                'header' => Mage::helper('epicor_comm')->__('Description'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text',
                'filter' => false
            ),
            'additionaltext' => array(
                'header' => Mage::helper('epicor_comm')->__('Additional Info'),
                'align' => 'left',
                'index' => 'additional_text',
                'type' => 'text',
                'filter' => false
            ),
            'contract_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Contract'),
                'align' => 'left',
                'index' => 'contract_code',
                'type' => 'text',
                'renderer' => new  Epicor_Customerconnect_Block_List_Renderer_ContractCode(),
            ),
            'price' => array(
                'header' => Mage::helper('epicor_comm')->__('Price'),
                'align' => 'right',
                'index' => 'price',
                'type' => 'number',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Currency(),
                'filter' => false
            ),
            'qty' => array(
                'header' => Mage::helper('epicor_comm')->__('Qty'),
                'align' => 'center',
                'index' => 'qty',
                'type' => 'number',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Qty(),
                'filter' => false
            ),
            'unit_of_measure_description' => array(
                'header' => Mage::helper('epicor_comm')->__('UOM'),
                'align' => 'left',
                'index' => 'unit_of_measure_description',
                'type' => 'text',
                'filter' => false
            ),
            'location' => array(
                'header' => Mage::helper('epicor_comm')->__('Location'),
                'align' => 'left',
                'index' => 'location_code',
                'type' => 'text',
                'filter' => false,
                'renderer' => new Epicor_Customerconnect_Block_List_Renderer_Location(),
            ),
            'line_value' => array(
                'header' => Mage::helper('epicor_comm')->__('Total Price'),
                'align' => 'right',
                'index' => 'line_value',
                'type' => 'number',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Currency(),
                'filter' => false
            ),
            'shipments' => array(
                'header' => Mage::helper('epicor_comm')->__(''),
                'align' => 'left',
                'index' => 'shipments',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Shipping(),
                'type' => 'text',
                'filter' => false,
                'keep_data_format' => 1,
                'column_css_class' => "expand-content",
                'header_css_class' => "expand-content"
            ),
        );
        //remove column if lists is disabled
        if(!Mage::getStoreConfigFlag('epicor_lists/global/enabled')){
            unset($columns['contract_code']);
        }

        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        $showLoc = ($locHelper->isLocationsEnabled()) ? $locHelper->showIn('cc_orders') : false;

        if (!$showLoc) {
            unset($columns['location']);
        }

        return $columns;
    }

    public function getRowUrl($row)
    {
        return null;
    }

}
