<?php

class Epicor_Comm_Block_Checkout_Onepage_Shipping_Dates extends Mage_Checkout_Block_Onepage_Abstract {

    protected $_available_dates;

    protected function _construct() {
        $this->getCheckout()->setStepData('shipping_dates', array(
            'label' => Mage::helper('checkout')->__('Available Delivery Dates'),
            'is_show' => $this->isShow()
        ));
        parent::_construct();
    }

    public function isShow() {
        return Mage::getStoreConfigFlag('epicor_comm_enabled_messages/dda_request/active');
    }

    public function showAsList() {
        return Mage::getStoreConfigFlag('epicor_comm_enabled_messages/dda_request/showaslist');
    }

    public function getAvailableDates() {
        
        if (!isset($this->_available_dates)) {
            $dda = Mage::getModel('epicor_comm/message_request_dda');
            /* @var $dda Epicor_Comm_Model_Message_Request_Dda */
            if($dda->isActive()) {
                $dda->setQuote($this->getQuote());
                $dda->sendMessage();
            }
            $this->_available_dates = $dda->getDates();
        }
        return $this->_available_dates;
    }

    public function getDefaultAvailableDate() {
        $default_shipping_days = Mage::getStoreConfig('epicor_comm_enabled_messages/gor_request/daystoship');
        $date = '1970-01-01'; //date('Y-m-d', strtotime('+'.$default_shipping_days.' days'));
        return $date;
    }

}