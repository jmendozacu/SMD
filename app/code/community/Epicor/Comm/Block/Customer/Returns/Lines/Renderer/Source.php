<?php

class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Source extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $source = $row->getSourceType();

        switch ($source) {
            case 'order':
                if ($helper->getReturnUserType() != 'b2b') {
                    $sourceRef = Mage::registry('source_ref');
                    $order = !isset($sourceRef[$row->getOrderNumber()]) ? $helper->findLocalOrder($row->getOrderNumber()) : $sourceRef[$row->getOrderNumber()];
                    if ($order && !$order->getIsObjectNew()) {
                        $html = $this->__('Order #') . $order->getIncrementId();
                    } else {
                        $html = $this->__('Order #') . $row->getOrderNumber();
                    }
                    $sourceRef[$row->getOrderNumber()] = $order;
                    Mage::unregister('source_ref');
                    $sourceRef = Mage::register('source_ref', $sourceRef);
                } else {
                    $html = $this->__('Order #') . $row->getOrderNumber();
                }
                break;
            case 'invoice':
                $html = $this->__('Invoice #') . $row->getInvoiceNumber();
                break;
            case 'serial':
                $html = $this->__('Serial #') . $row->getSerialNumber();
                break;
            case 'shipment':
                $html = $this->__('Shipment #') . $row->getShipmentNumber();
                break;
            default:
                $html = $this->__('SKU');
                break;
        }

        return '<span class="return_line_source_label">' . $html . '</span>';
    }

}
