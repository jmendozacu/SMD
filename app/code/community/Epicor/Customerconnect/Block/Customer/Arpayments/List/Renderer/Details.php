<?php

/**
 * AR Payment link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Details extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        $html = '';
        $id = $row->getId();
        if (!empty($id)) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNumber = $helper->getErpAccountNumber();
            $invoice = $helper->urlEncode($helper->encrypt($erpAccountNumber . ']:[' . $row->getId()));             
            $return = Mage::getUrl('customerconnect/orders/');
            $jsonData = $this->showDetails($row);
            $strId = preg_replace('/\s+/', '', $row->getId());
            $html = '<a href="#" onclick="arpayments.openpopup(\'' . $invoice . '\',\'' . $strId . '\'); return false;">' . $this->__('Details') . '</a>';
            $html .='<input type="hidden" class="arpaymentjson" name="arpaymentjson[]" id="arpaymentjson_'.$invoice.'" value=\''.$jsonData.'\'>';
        }
        return $html;
    }
    
    public function showDetails($row) {
       $invoiceDate = $this->processDate($row->getInvoiceDate());
       $dueDate = $this->processDate($row->getDueDate());
       $data = array();
       $data['invoiceNo'] = $row->getInvoiceNumber();
       $data['invoiceDate'] = ($invoiceDate) ? $invoiceDate : "N/A";
       $data['dueDate']    = ($dueDate) ? $dueDate : "N/A";
       $data['invoiceAmnt'] = $row->getOriginalValue();
       $data['invoiceBalance'] = $row->getOutstandingValue();
       $data['invoicePayment'] = $row->getPaymentValue();
       /*$data['deliveryAddressName'] = $row->getDeliveryAddressName();
       $data['invoiceNo'] = $row->getDeliveryAddressAddress1();
       $data['address1'] = $row->getDeliveryAddressAddress1();
       $data['address2'] = $row->getDeliveryAddressAddress2();
       $data['address3'] = $row->getDeliveryAddressAddress3();
       $data['city'] = $row->getDeliveryAddressCity();
       $data['county'] = $row->getDeliveryAddressCounty();
       $data['country'] = $row->getDeliveryAddressCountry();
       $data['postcode'] = $row->getDeliveryAddressPostcode();*/
       $data['deliveryAddress'] = $row->getDeliveryAddress();
       return json_encode($data);
    }
    
    /**
     * 
     * Get processed date
     * @param string
     * @return string
     */
    public function processDate($rawDate=NULL)
    {
        if ($rawDate) {
            $timePart = substr($rawDate, strpos($rawDate, "T") + 1);
            if (strpos($timePart, "00:00:00") !== false) {
                $processedDate = Mage::helper('customerconnect')->getLocalDate($rawDate, Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, false);
            } else {
                $processedDate = Mage::helper('customerconnect')->getLocalDate($rawDate, Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true);
            }
        } else {
            $processedDate = '';
        }
        return $processedDate;
    }    

}