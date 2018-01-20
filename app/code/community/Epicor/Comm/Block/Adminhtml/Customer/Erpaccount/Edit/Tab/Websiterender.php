<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Websiterender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value) {
            $collection = Mage::getModel('core/website')->load($value, 'website_id');
            $val = $collection->getName();
            return $val;
        }
    }

}
