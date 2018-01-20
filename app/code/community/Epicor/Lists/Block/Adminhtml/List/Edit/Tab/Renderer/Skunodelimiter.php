<?php

class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Skunodelimiter extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $fullsku = $row->getSku();
        $delimiter = Mage::helper('epicor_lists/messaging_customer')->getUOMSeparator();
        $sku = explode($delimiter, $fullsku);
       
        return $sku[0];
    }

}

