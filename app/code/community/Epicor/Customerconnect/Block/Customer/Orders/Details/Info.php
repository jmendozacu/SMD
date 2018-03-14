<?php

/**
 * Block class for Order details information
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Info extends Epicor_Customerconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();
        $order = Mage::registry('customer_connect_order_details');
        $orderDate = $order->getOrderDate();
        $requiredDate = $order->getRequiredDate();
        $session = Mage::getSingleton('customer/session');
        $customer = $session->getCustomer();
        $storeId = $customer->getStoreId();
        $allowTaxExemptRef = Mage::helper('epicor_comm')->isTaxExemptionAllowed($storeId, $customer->getErpaccountId());
        $shipStatus = Mage::helper('epicor_comm')->shipStatus(null, $storeId);
        $additionalreference = Mage::helper('epicor_comm')->additionalReference(null, $storeId);

        $this->_infoData = array(
            $this->__('Order Date :') => $this->processDate($orderDate) ? $this->processDate($orderDate) : $this->__('N/A'),
            $this->__('Need By :') => $this->processDate($requiredDate) ? $this->processDate($requiredDate) : $this->__('N/A'),
            $this->__('Terms :') => $order->getPaymentTerms(),
            $this->__('PO Number :') => $order->getCustomerReference(),
            $this->__('Sales Person :') => $order->getSalesRep()->getName(),
            $this->__('Ship Via :') => $order->getDeliveryMethod(),
            $this->__('FOB :') => $order->getFob(),
            $this->__('Tax Id :') => $order->getTaxid(),
        );

        if (Mage::getStoreConfigFlag('epicor_lists/global/enabled')) {
            $this->_infoData[$this->__('Contract : ')] = Mage::helper('epicor_comm')->retrieveContractTitle($order->getContractCode());
        }
        if ($allowTaxExemptRef) {
            $this->_infoData[$this->__('Tax Exempt Reference :')] = $order->getTaxExemptReference();
        }
        if ($additionalreference['visible']) {
            $this->_infoData[$this->__('Additional Reference:')] = $order->getAdditionalReference();
        }
        if ($shipStatus['visible']) {
            $this->_infoData[$this->__('Ship Status:')] = $order->getShipStatus();
        }
        $this->setTitle($this->__('Order Information :'));
    }

    /**
     * 
     * Get processed date
     * @param string
     * @return string
     */
    public function processDate($rawDate) {
        if ($rawDate) {
            $timePart = substr($rawDate, strpos($rawDate, "T") + 1);
            if (strpos($timePart, "00:00:00") !== false) {
                $processedDate = $this->getHelper()->getLocalDate($rawDate, Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, false);
            } else {
                $processedDate = $this->getHelper()->getLocalDate($rawDate, Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true);
            }
        } else {
            $processedDate = '';
        }
        return $processedDate;
    }

}
