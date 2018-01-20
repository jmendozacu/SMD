<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Generalinfo extends Epicor_Supplierconnect_Block_Customer_Info
{

    public function _construct()
    {
        parent::_construct();

        $rfq = Mage::registry('supplier_connect_rfq_details');

        $rfqDate = ($rfq->getRfqDate()) ? Mage::helper('supplierconnect')->getLocalDate($rfq->getRfqDate(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : '';
        $rfqRespondDate = ($rfq->getRespondDate()) ? Mage::helper('supplierconnect')->getLocalDate($rfq->getRespondDate(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : '';
        $rfqDecisionDate = ($rfq->getDecisionDate()) ? Mage::helper('supplierconnect')->getLocalDate($rfq->getDecisionDate(), Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : '';

        $this->_infoData = array(
            $this->__('RFQ Number: ') => $rfq->getRfqNumber(),
            $this->__('RFQ Line: ') => $rfq->getLine(),
            $this->__('RFQ Date: ') => $rfqDate,
            $this->__('Respond By: ') => $rfqRespondDate,
            $this->__('RFQ Decision Date: ') => $rfqDecisionDate,
        );

        $this->_extraData = array(
            $this->__('Header Comments') => $rfq->getHeaderComment(),
        );

        $this->setTitle($this->__('General Information'));
        $this->setOnLeft(true);
        $this->setColumnCount(1);
    }

    public function _toHtml()
    {
        $rfq = Mage::registry('supplier_connect_rfq_details');
        $html = '';

        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */
        $rfq = base64_encode(serialize($helper->varienToArray($rfq)));
        $html = '<input type="hidden" name="old_data" value="' . $rfq . '" />';

        $html .= parent::_toHtml();
        return $html;
    }

}
