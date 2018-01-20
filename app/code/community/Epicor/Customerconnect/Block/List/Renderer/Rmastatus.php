<?php

/**
 * RMA status display, converts an erp RMA status code to mapped RMA status
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Rmastatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('customerconnect/messaging');

        $index = $this->getColumn()->getIndex();
        return $helper->getRmaStatusDescription($row->getData($index));
    }

}
