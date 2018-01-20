<?php

/**
 * Order status "Open" display, shows Yes / No based on status value
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_Orderstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $index = $this->getColumn()->getIndex();
        $status = $row->getData($index);

        return ($status == 'O') ? 'Open' : 'Closed';
    }

}
