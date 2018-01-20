<?php

/**
 * Address state display
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_State extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

//        $helper = Mage::helper('supplierconnect');
//        /* @var $helper Epicor_Supplierconnect_Helper_Data */
//        $countryCode = $helper->getCountryCodeForDisplay($row->getDeliveryAddressCountry());
//        $region = $helper->getRegionFromCountyName($countryCode, $row->getDeliveryAddressCounty());
//
//        return ($region) ? $region->getName() : $row->getDeliveryAddressCounty();
        
        return $row->getDeliveryAddressCounty();
    }

}
