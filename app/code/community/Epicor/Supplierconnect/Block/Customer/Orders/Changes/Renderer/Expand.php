<?php

/**
 * Expand column for changed orders list
 *
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Changes_Renderer_Expand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $html = '';

        $linesMsg = $row->getLines();
        $lines = false;

        if (!empty($linesMsg)) {
            $lines = $linesMsg->getLine();
        }

        if (!empty($lines)) {
            $html = '<span class="plus-minus" id="changes-' . $row->getId() . '">+</span>';
        }

        return $html;
    }

}
