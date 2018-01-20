<?php

/**
 * Return line delete column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return_Line */

        $checked = $row->getToBeDeleted() == 'Y' ? ' checked="checked"' : '';

        $html = '<input type="checkbox" class="return_line_delete" name="lines[' . $row->getUniqueId() . '][delete]"' . $checked . ' />';

        return $html;
    }

}
