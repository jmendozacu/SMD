<?php

class Epicor_Comm_Block_Customer_Address_Renderer_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    //   protected $updateList = Array();
    public function render(Varien_Object $row) {
        $detailsArray['id'] = $row->getEntityId();
        $detailsArray['company'] = $row->getCompany();
        $detailsArray['street'] = $row->getStreet();
        $detailsArray['city'] = $row->getCity();
        $detailsArray['region'] = $row->getRegion();
        $detailsArray['country'] = $row->getCountry();
        $detailsArray['postcode'] = $row->getPostcode();
        $jsonArray = json_encode($detailsArray);

        $html = '<input type="text" class="details" name="details"';
        $html .= '" style="display:none" value="' . htmlspecialchars($jsonArray) . '"/> ';
        $html .= $row->getId();
        return $html;
    }

}
