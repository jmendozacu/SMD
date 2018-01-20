<?php

class Epicor_Customerconnect_Block_Customer_Invoices_Details_Data extends Epicor_Customerconnect_Block_Customer_Info
{

    public function _construct()
    {
        parent::_construct();
        $invoices = Mage::registry('customer_connect_invoices_details');
        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $erp_account_number = $helper->getErpAccountNumber();

        $order_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $invoices->getOurOrderNumber()));

        $renderer = new Epicor_Customerconnect_Block_List_Renderer_Linkorder();
        $columnData = new Varien_Object();
        $columnData->setIndex('our_order_number');
        $renderer->setColumn($columnData);
        $orderLink = $renderer->render($invoices);
        $invoiceDate = $invoices->getDate();
        $dueDate = $invoices->getDueDate();
        $this->_infoData = array(
            $this->__('Invoice Date :') => $this->processDate($invoiceDate) ? $this->processDate($invoiceDate) : $this->__('N/A'),
            $this->__('Due By :') => $this->processDate($dueDate) ? $this->processDate($dueDate) : $this->__('N/A'),
            $this->__('Terms :') => $invoices->getPaymentTerms(),
            $this->__('PO Number :') => $invoices->getCustomerReference(),
            $this->__('Ship Via :') => $invoices->getDeliveryMethod(),
            $this->__('Sales Person :') => $invoices->getSalesRep() ? $invoices->getSalesRep()->getName() : null,
            $this->__('Order Number :') => $orderLink,
            $this->__('FOB :') => $invoices->getFob(),
            $this->__('Reseller Id :') => $invoices->getReseller() ? $invoices->getReseller()->getNumber() : null
        );
        if (Mage::getStoreConfigFlag('epicor_lists/global/enabled')) {
            $this->_infoData[$this->__('Contract : ')] = $invoices->getContractCode() ? Mage::helper('epicor_comm')->retrieveContractTitle($invoices->getContractCode()) : null;
        }

        $this->setTitle($this->__('Invoice Information :'));
    }
    

    /**
     * 
     * Get processed date
     * @param string
     * @return string
     */
    public function processDate($rawDate)
    {
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
