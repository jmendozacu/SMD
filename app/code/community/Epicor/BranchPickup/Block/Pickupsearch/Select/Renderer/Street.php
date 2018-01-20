<?php

class Epicor_BranchPickup_Block_Pickupsearch_Select_Renderer_Street extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    //   protected $updateList = Array();
    public function render(Varien_Object $row) {
        $address1 = $row->getAddress1();
        $address2 = $row->getAddress2();
        $address3 = $row->getAddress3();
        $detailsArray = array($address1,$address2,$address3);
        $string = implode(',', array_filter($detailsArray, 'strlen'));
        return $string;
    }

}
