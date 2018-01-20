<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Shipping
 *
 * @author Paul.Ketelle
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Expand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */      
        $html = '';

        $shipmentMsg = $row->getShipments();
        $shipments = false;

        if (!empty($shipmentMsg)) {
            $shipments = $shipmentMsg->getShipment();
        }
        
        $row->setUniqueId(uniqid());

        if ($shipments) {
            $html = '<span class="plus-minus" id="shipments-' . $row->getUniqueId() . '">+</span>';
        }
        return $html;
    }

}


