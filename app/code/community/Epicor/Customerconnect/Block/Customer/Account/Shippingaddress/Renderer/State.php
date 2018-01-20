<?php

class Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    //   protected $updateList = Array();
    public function render(Varien_Object $row) {

//        $helper = Mage::helper('customerconnect');
//        /* @var $helper Epicor_Customerconnect_Helper_Data */
//        $countryCode = $helper->getCountryCodeForDisplay($row->getCountry());
//        $region = $helper->getRegionFromCountyName($countryCode, $row->getCounty());
//
//        return ($region) ? $region->getName() : $row->getCounty();
        return $row->getCounty();
    }

}
