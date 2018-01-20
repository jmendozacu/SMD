<?php

/**
 * Order status display, converts an order status code to magento order status
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Orderstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $helper = Mage::helper('epicor_comm/messaging');

        $index = $this->getColumn()->getIndex();
        return $helper->getOrderStatusDescription($row->getData($index));
    }

}
