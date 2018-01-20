<?php

/**
 * Returns creation page, Products block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Products extends Epicor_Comm_Block_Customer_Returns_Abstract
{

    private $_findByOptions;

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Products'));
        $this->setTemplate('epicor_comm/customer/returns/products.phtml');
    }

    public function getLinesHtml()
    {
        return Mage::app()->getLayout()->createBlock('epicor_comm/customer_returns_lines')->toHtml();
    }

    public function getFindLinesByOptions()
    {
        if ($this->_findByOptions === null) {
            $this->_findByOptions = array();

            $allowed = array();

            if (!$this->checkConfigFlag('allow_mixed_return')) {
                foreach ($this->getReturnLines() as $line) {
                    /* @var $line Epicor_Comm_Model_Customer_Return_Line */
                    $allowed[] = $line->getSourceType();
                }
            }

            if (empty($allowed)) {
                $allowed = array('order', 'invoice', 'shipment', 'serial');
            }
            
            $order = $this->configHasValue('find_lines_by', 'order_number');
            $invoice = $this->configHasValue('find_lines_by', 'invoice_number');
            $shipment = $this->configHasValue('find_lines_by', 'shipment_number');
            $serial = $this->configHasValue('find_lines_by', 'serial_number');

            if ($order && in_array('order', $allowed)) {
                $this->_findByOptions[] = array(
                    'value' => 'order',
                    'label' => $this->__('Order Number'),
                );
            }

            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */

            if ($invoice && $helper->isMessageEnabled('customerconnect', 'cuid')
                && in_array('invoice', $allowed)) {
                $this->_findByOptions[] = array(
                    'value' => 'invoice',
                    'label' => $this->__('Invoice Number'),
                );
            }

            if ($shipment && $helper->isMessageEnabled('customerconnect', 'cuss')
                && $helper->isMessageEnabled('customerconnect', 'CUSD')
                && in_array('shipment', $allowed)) {
                $this->_findByOptions[] = array(
                    'value' => 'shipment',
                    'label' => $this->__('Shipment Number'),
                );
            }

            if ($serial && $helper->isMessageEnabled('epicor_comm', 'csns')
                && in_array('serial', $allowed)) {
                $this->_findByOptions[] = array(
                    'value' => 'serial',
                    'label' => $this->__('Serial Number'),
                );
            }
        }

        return $this->_findByOptions;
    }

    public function addMethodAllowed($type)
    {
        $allowed = true;

        if (!$this->checkConfigFlag('allow_mixed_return')) {
            $lines = $this->getReturnLines();

            foreach ($lines as $line) {
                /* @var $line Epicor_Comm_Model_Customer_Return_Line */
                $source = $line->getSourceType();
                if ($type == 'addsku') {
                    if ($source != 'sku') {
                        $allowed = false;
                    }
                } else {
                    if ($source == 'sku') {
                        $allowed = false;
                    }
                }
            }
        }

        return $allowed;
    }

    public function getReturnLines()
    {
        $return = Mage::registry('return_model');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $linesData = array();
        if ($return) {
            $linesData = $return->getLines() ? : array();
        }

        return $linesData;
    }

}
