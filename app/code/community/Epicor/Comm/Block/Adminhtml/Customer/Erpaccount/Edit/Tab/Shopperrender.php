<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Shopperrender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());

        if ($value == 1) {
            $val = "<input name=shoppers[]" . " type='checkbox' value=" . $row->getId() . " checked=checked>";
        } else {
            $val = "<input name=shoppers[]" . " type='checkbox' value=" . $row->getId() . ">";
        }
        return $val;
    }

}
