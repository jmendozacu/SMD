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
class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $html = '';

        $shipments = ($row->getShipments()) ? $row->getShipments()->getasarrayShipment() : array();

        if (count($shipments) > 0) {
            $html = '</td></tr><tr id="row-shipments-' . $row->getUniqueId() . '" style="display: none;"><td colspan="8" class="shipping-row">
            <table class="expand-table">
                <thead>
                    <tr class="headings">
                        <th>' . $this->__('Status') . '</th>
                        <th>' . $this->__('Date') . '</th>
                        <th>' . $this->__('Quantity') . '</th>
                        <th>' . $this->__('Ship Via') . '</th>
                        <th>' . $this->__('Pack Slip') . '</th>
                    </tr>
                </thead>
                <tbody>
            ';
            // pick out account no from passed parm
            $order_requested = explode(']:[', $helper->decrypt($helper->urlDecode(Mage::app()->getRequest()->getParam('order'))));

            $accessHelper = Mage::helper('epicor_common/access');
            /* @var $helper Epicor_Common_Helper_Access */
            $hasAccess = $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Shipments', 'details', '', 'Access');
            
            foreach ($shipments as $shipment) {

                $helper = Mage::helper('customerconnect');
                /* @var $helper Epicor_Customerconnect_Helper_Data */
                $erp_account_number = $helper->getErpAccountNumber();

                if (!empty($shipment['shipment_date'])) {
                    $shipmentDate = $helper->getLocalDate($shipment['shipment_date'], Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true);
                } else {
                    $shipmentDate = $this->__('N/A');
                }

                if($hasAccess) {
                    $packing_slip_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $shipment['packing_slip'] . ']:[' . $order_requested[1]));
                    $new_url = Mage::getUrl('customerconnect/shipments/details', array('shipment' => $packing_slip_requested, 'back' => $helper->urlEncode(Mage::getUrl('*/*/*',$this->getRequest()->getParams()))));
                    $packSlipLink = '<a href="' . $new_url . '">' . $shipment['packing_slip'] . '</a>';
                } else {
                    $packSlipLink = $shipment['packing_slip'];
                }
                
                $html .= '
                  <tr>
                    <td>' . $shipment['shipment_status'] . '</td>
                    <td>' . $shipmentDate . '</td>
                    <td>' . floatval($shipment['quantity']) . '</td>
                    <td>' . $shipment['delivery_method'] . '</td>
                    <td>' . $packSlipLink . '</td>
                  </tr>
                    ';
            }
            $html .= '</tbody></table>';
        }

        return $html;
    }

}

