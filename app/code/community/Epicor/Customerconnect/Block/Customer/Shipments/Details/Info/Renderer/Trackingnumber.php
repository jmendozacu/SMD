<?php

class Epicor_Customerconnect_Block_Customer_Shipments_Details_Info_Renderer_Trackingnumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row) {
        if (!is_null($row->getTrackingUrl())){
            $html = '<a href="' . $row->getTrackingUrl() . '" target="_blank" >' . $row->getTrackingNumber() . '</a>';
        } else {
            $html = $row->getTrackingNumber();
        }
        return $html;
    }

}

