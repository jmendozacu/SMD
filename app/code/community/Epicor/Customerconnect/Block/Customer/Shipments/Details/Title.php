<?php

/**
 * Shipment Details page title
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Shipments_Details_Title
        extends Epicor_Customerconnect_Block_Customer_Title
{

    protected $_reorderType = 'Shipments';
    protected $_returnType = 'Shipment';
    
    public function _construct()
    {
        parent::_construct();
        $this->_setTitle();
        $this->_setReorderUrl();
        $this->_setReturnUrl();
    }

    /**
     * Sets the page title
     */
    protected function _setTitle()
    {
        $shipment = Mage::registry('customer_connect_shipments_details');
        $this->_title = $this->__('Pack Slip : %s', $shipment->getPackingSlip());
    }

    /**
     * Sets the Reorder link url
     */
    protected function _setReorderUrl()
    {
        $shipment = Mage::registry('customer_connect_shipments_details');

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $this->_reorderUrl = $helper->getShipmentReorderUrl($shipment, Mage::helper('core/url')->getCurrentUrl());
    }
    
    /**
     * Sets the Return link url
     */
    protected function _setReturnUrl()
    {
        $shipment = Mage::registry('customer_connect_shipments_details');

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $this->_returnUrl = $helper->getShipmentReturnUrl($shipment);
    }

}
