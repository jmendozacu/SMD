<?php

/**
 * Order link display
 *
 * @author Sean Flynn
 */
class   Epicor_Customerconnect_Block_List_Renderer_Allshipments extends Epicor_Common_Block_Renderer_Encodedlinkabstract
{
    public function render(Varien_Object $row) { 
    $html = '';
        $id = $row->getPackingSlip();
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Shipments', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erp_account_number = $helper->getErpAccountNumber();
            $shipments = Mage::registry('customer_connect_shipments_details');
            $packing_slip_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getPackingSlip(). ']:[ordernumberempty'));

            $new_url = Mage::getUrl('customerconnect/shipments/details', array('shipment' => $packing_slip_requested, 'back' => $helper->urlEncode(Mage::getUrl('*/*/*', $this->getRequest()->getParams()))));

            if (!empty($id)) {
                $html = '<a href="' . $new_url . '">' . $id . '</a>';
            }
        } else {
            if (!empty($id)) {
                $html = $id;
            }
        }

        return $html;

    }
}    
