<?php

/**
 * Order status "confirmed" display, shows confirmed / rejected based on status value
 *
 * @author Gareth.James
 */
class Epicor_Supplierconnect_Block_Customer_Orders_List_Renderer_Confirmed extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {

        $index = $this->getColumn()->getIndex();
        $status = $row->getData($index);
        
        switch ($status) {
            case 'C':
                return 'Confirmed';
                break;
            case 'R':
                return 'Rejected';
                break;
            case 'NC':
                return 'Not Confirmed';
                break;
            
        }
    }

}
