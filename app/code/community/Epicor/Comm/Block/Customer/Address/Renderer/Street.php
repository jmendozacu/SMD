<?php

class Epicor_Comm_Block_Customer_Address_Renderer_Street extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    //   protected $updateList = Array();
    public function render(Varien_Object $row) {
        $detailsArray= $row->getStreet();
        if(is_array($detailsArray)) {
            $string = implode(',', array_filter($detailsArray, 'strlen'));
        } else {
            $string = $detailsArray;
        }
        return $string;
    }

}