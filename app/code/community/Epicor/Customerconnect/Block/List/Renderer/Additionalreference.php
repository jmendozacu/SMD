<?php

/**
 * Block renderer class for Ship status  additional reference field
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_List_Renderer_Additionalreference extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $storeId = Mage::app()->getStore()->getStoreId();
        $additionalreference = Mage::helper('epicor_comm')->additionalReference(null, $storeId);
        $visible = $additionalreference['visible'];
        return ($visible) ? $row->getAdditionalReference() : null;
    }

}
