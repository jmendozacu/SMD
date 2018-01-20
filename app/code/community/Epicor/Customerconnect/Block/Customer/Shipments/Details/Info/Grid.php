<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Shipments_Details_Info_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customerconnect_shipping_info');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('customerconnect');
        $this->setMessageType('cusd');
        $this->setIdColumn('order_number');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $order = Mage::registry('customer_connect_shipments_details');

        $lines = ($order->getLines()) ? $order->getLines()->getasarrayLine() : array();

        if (!empty($lines)) {
            foreach ($lines as $line) {
                $delivered = $line->getQuantity()->getDelivered();          // couldn't do direct as with quantity, so did it this way
                $toFollow = $line->getQuantity()->getToFollow();
                $line->setQuantity($line->getQuantity()->getOrdered());
                $line->setDelivered($delivered);
                $line->setToFollow($toFollow);
            }
        }

        $this->setCustomData((array) $lines);
    }

    protected function _getColumns()
    {

        $columns = array(
            'order_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Order'),
                'align' => 'left',
                'index' => 'order_number',
                'renderer' => new Epicor_Customerconnect_Block_List_Renderer_Linkorder(),
                'type' => 'range',
            ),
            'quantity' => array(
                'header' => Mage::helper('epicor_comm')->__('Order Qty'),
                'align' => 'left',
                'index' => 'quantity',
                'keys' => array(
                    'quantity',
                    'delivered',
                    'to_follow',
                ),
                'labels' => array(
                    'quantity' => 'Quantity',
                    'delivered' => 'Delivered',
                    'to_follow' => 'To Follow',
                ),
                'join' => '<br />',
                'renderer' => new Epicor_Common_Block_Renderer_Composite(),
                'type' => 'text'
            ),
            'delivered' => array(
                'header' => Mage::helper('epicor_comm')->__('Delivered'),
                'align' => 'left',
                'index' => 'delivered',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type' => 'text'
            ),
            'to_follow' => array(
                'header' => Mage::helper('epicor_comm')->__('To Follow'),
                'align' => 'left',
                'index' => 'to_follow',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type' => 'text'
            ),
            'location' => array(
                'header' => Mage::helper('epicor_comm')->__('Location'),
                'align' => 'left',
                'index' => 'location_code',
                'type' => 'text',
                'filter' => false,
                'renderer' => new Epicor_Customerconnect_Block_List_Renderer_Location(),
            ),
            'unit_of_measure_description' => array(
                'header' => Mage::helper('epicor_comm')->__('UOM'),
                'align' => 'left',
                'index' => 'unit_of_measure_description',
                'type' => 'text'
            ),
            'product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Part Number'),
                'align' => 'left',
                'index' => 'product_code',
                'type' => 'text'
            ),
            'description' => array(
                'header' => Mage::helper('epicor_comm')->__('Description'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text'
            ),
            'tracking_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Tracking Number'),
                'align' => 'left',
                'column_css_class' => 'tracking_number',
                'index' => 'tracking_number',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Shipments_Details_Info_Renderer_Trackingnumber(),
                'type' => 'range'
            ),
            'invoice_number' => array(
                'header' => Mage::helper('epicor_comm')->__('Invoice Number'),
                'align' => 'left',
                'index' => 'invoice_number',
                'renderer' => new Epicor_Customerconnect_Block_List_Renderer_Linkinvoice(),
                'type' => 'range'
            ),
            'invoice_status' => array(
                'header' => Mage::helper('epicor_comm')->__('Invoice Status'),
                'align' => 'left',
                'index' => 'invoice_status',
                'type' => 'text',
                'renderer' => new Epicor_Customerconnect_Block_List_Renderer_Invoicestatus()
            ),
        );

        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        $showLoc = ($locHelper->isLocationsEnabled()) ? $locHelper->showIn('cc_shipments') : false;

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
