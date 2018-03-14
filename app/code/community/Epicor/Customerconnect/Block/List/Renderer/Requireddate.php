<?php

/**
 * Block renderer class for Ship status required date
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_List_Renderer_Requireddate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $storeId = Mage::app()->getStore()->getStoreId();
        $requiredDate = Mage::helper('epicor_comm')->requiredDate(null, $storeId);
        $visible = $requiredDate['visible'];
        return ($visible) ? $row->getRequiredDate() : null;
    }

}
