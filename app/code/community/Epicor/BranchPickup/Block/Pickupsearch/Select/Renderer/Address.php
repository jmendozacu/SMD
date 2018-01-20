<?php

class Epicor_BranchPickup_Block_Pickupsearch_Select_Renderer_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    //   protected $updateList = Array();
    public function render(Varien_Object $row) {
        $helper = Mage::helper('epicor_branchpickup');
        /* var Epicor_BranchPickup_Helper_Data  */
        $getData = $helper->getPickupAddress($row->getCode());
        $jsonArray = json_encode($getData);
        $html = '<input type="text" id="branchpickup_'.trim($row->getCode()).'" class="branchsearchdetails" name="branchsearchdetails"';
        $html .= ' style="display:none" value="' . htmlspecialchars($jsonArray) . '"/> ';
        $html .= $row->getId();
        return $html;
    }

}
