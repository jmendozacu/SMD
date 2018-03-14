<?php
/**
 * AR Payments Payment
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Details_Lines_Renderer_Packingslip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row) {

        $html = '';
        $id = $row->getPackingSlip();
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Shipments', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erp_account_number = $helper->getErpAccountNumber();
            $invoice = Mage::registry('customer_connect_invoices_details');
            $packing_slip_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getPackingSlip() . ']:[' . $invoice->getOurOrderNumber()));

            $new_url = Mage::getUrl('customerconnect/shipments/details', array('shipment' => $packing_slip_requested, 'back' => $helper->urlEncode(Mage::getUrl('*/*/*', $this->getRequest()->getParams()))));

            if (!empty($id)) {
                $html = '<a target="_blank" href="' . $new_url . '">' . $id . '</a>';
            }
        } else {
            if (!empty($id)) {
                $html = $id;
            }
        }

        return $html;
    }

}
