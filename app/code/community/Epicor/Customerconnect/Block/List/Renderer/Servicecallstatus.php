<?php

/**
 * Service Call status display, converts an erp Service Call status code to mapped Service Call status
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Servicecallstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('customerconnect/messaging');
        $index = $this->getColumn()->getIndex();
        return $helper->getServicecallStatusDescription($row->getData($index));
    }

}
