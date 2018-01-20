<?php

class Epicor_Customerconnect_Block_Customer_Account_Shippingaddress_Renderer_Shippingaddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    //   protected $updateList = Array();
    public function render(Varien_Object $row)
    {
        $detailsArray['name'] = $row->getName();
        $detailsArray['address1'] = $row->getAddress1();
        $detailsArray['address2'] = $row->getAddress2();
        $detailsArray['address3'] = $row->getAddress3();
        $detailsArray['city'] = $row->getCity();

        $detailsArray['county'] = $row->getCounty();

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $countryCode = $helper->getCountryCodeForDisplay($row->getCountry());
        $region = $helper->getRegionFromCountyName($countryCode, $row->getCounty());
        $countyId = ($region) ? $region->getId() : 0;

        $detailsArray['county_id'] = $countyId;

        $detailsArray['country_code'] = $countryCode;
        $detailsArray['country'] = $row->getCountry();
        $detailsArray['postcode'] = $row->getPostcode();
        $detailsArray['telephone'] = $row->getTelephoneNumber();
        $detailsArray['fax'] = $row->getFaxNumber();
        $detailsArray['address_code'] = $row->getAddressCode();
        $detailsArray['email'] = $row->getEmail();
        $jsonArray = json_encode($detailsArray);

        $html = '<input type="text" class="details" name="details"';
        $html .= '" style="display:none" value="' . htmlspecialchars($jsonArray) . '"/> ';
        $html .= $row->getName();
        return $html;
    }

}
