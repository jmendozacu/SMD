<?php

/**
 * Quantity display, converts a row value to qty display
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Orders_Details_Parts_Renderer_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $index = $this->getColumn()->getIndex();
        if ($row->getData($index)) {
            $value = $row->getData($index) * 1;
        } else {
            $value = $row->getData($index);
        }
        return $value;
    }

}
