<?php

class Epicor_Customerconnect_Block_Customer_Shipments_Details_Date extends Epicor_Customerconnect_Block_Customer_Info
{

    // extended block_customer_info to get the helper
    public function _construct()
    {
        parent::_construct();
        $data = Mage::registry('customer_connect_shipments_details');
        //checks for valid time, if yes dispalys time along with date.
        preg_match('/T00:00:00/', $data->getShipmentDate(), $inValidTime);
        $allowTime = $inValidTime ? false : true;
        $this->_infoData = array(
            $this->__('Shipment Date :') => $data->getShipmentDate() ? $this->getHelper()->getLocalDate($data->getShipmentDate(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, $allowTime) : $this->__('N/A'),
            $this->__('Ship Via :') => $data->getDeliveryMethod(),
        );
        $this->setTitle($this->__('Packing Slip Information:'));
        $this->setColumnCount(2);
    }

}
