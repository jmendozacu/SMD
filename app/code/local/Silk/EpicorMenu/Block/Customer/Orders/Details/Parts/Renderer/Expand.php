<?php 
class Silk_EpicorMenu_Block_Customer_Orders_Details_Parts_Renderer_Expand extends Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Expand
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
            $html = '<span class="plus-minus" id="shipments-' . $row->getUniqueId() . '">+ Click to Expand</span>';
        }
        return $html;
    }
}
?>
