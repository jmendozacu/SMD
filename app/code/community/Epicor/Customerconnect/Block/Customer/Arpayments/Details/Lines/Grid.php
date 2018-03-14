<?php
/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
/**
 * Customer Orders list Grid config
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Lines_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customerconnect_invoices_lines');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('customerconnect');
        $this->setMessageType('cuid');
        $this->setIdColumn('invoice_number');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);

        $invoice = Mage::registry('customer_connect_invoices_details');

        $lines = ($invoice->getLines()) ? $invoice->getLines()->getasarrayLine() : array();

        if (!empty($lines)) {
            foreach ($lines as $line) {
                if ($line->getQuantities()) {
                    $delivered = $line->getQuantities()->getDelivered();
                    $toFollow = $line->getQuantities()->getToFollow();
                    $line->setQuantities($line->getQuantities()->getOrdered());
                    $line->setDelivered($delivered);
                    $line->setToFollow($toFollow);
                } else {
                    $line->setQuantities(0);
                    $line->setDelivered(0);
                    $line->setToFollow(0);
                }
            }
        }
        $this->setCustomData((array) $lines);
    }

    protected function _getColumns()
    {

        $columns = array(
            'quantities' => array(
                'header' => Mage::helper('epicor_comm')->__('Quantities'),
                'align' => 'left',
                'index' => 'quantities',
                'type' => 'number',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Invoices_Details_Lines_Renderer_Quantities()
            ),
            'delivered' => array(
                'header' => Mage::helper('epicor_comm')->__('Delivered'),
                'align' => 'left',
                'index' => 'delivered',
                'type' => 'number',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ),
            'to_follow' => array(
                'header' => Mage::helper('epicor_comm')->__('To Follow'),
                'align' => 'left',
                'index' => 'to_follow',
                'type' => 'number',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
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
                'index' => 'product_code'
            ),
            'description' => array(
                'header' => Mage::helper('epicor_comm')->__('Description'),
                'align' => 'left',
                'index' => 'description',
                'type' => 'text'
            ),
            'shipping_date' => array(
                'header' => Mage::helper('epicor_comm')->__('Shipping Date'),
                'align' => 'left',
                'column_css_class' => 'shipping_date',
                'index' => 'shipping_date',
                'type' => 'date'
            ),
            'packing_slip' => array(
                'header' => Mage::helper('epicor_comm')->__('Packing Slip'),
                'align' => 'left',
                'index' => 'packing_slip',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_Details_Lines_Renderer_Packingslip(),
                'type' => 'text'
            ),
            'contract_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Contract'),
                'align' => 'left',
                'index' => 'contract_code',
                'type' => 'text',
                'renderer' => new  Epicor_Customerconnect_Block_List_Renderer_ContractCode(),
            ),
            'price' => array(
                'header' => Mage::helper('epicor_comm')->__('Unit Price'),
                'align' => 'left',
                'column_css_class' => 'a-right',
                'index' => 'price',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency(),
                'type' => 'number'
            ),
            
             'line_value' => array(
                'header' => Mage::helper('epicor_comm')->__('Line Value'),
                'align' => 'left',
                'column_css_class' => 'a-right',
                'index' => 'line_value',
                'renderer' => new Epicor_Customerconnect_Block_Customer_Invoices_Details_Lines_Renderer_Currency(),
                'type' => 'number'
            ),
        );

        //remove column if lists is disabled
        if (!Mage::getStoreConfigFlag('epicor_lists/global/enabled')) {
            unset($columns['contract_code']);
        }


        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */
        $showLoc = ($locHelper->isLocationsEnabled()) ? $locHelper->showIn('cc_invoices') : false;

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
