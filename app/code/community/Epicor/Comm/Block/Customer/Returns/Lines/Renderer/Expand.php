<?php

/**
 * Return line row expand renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Expand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $html = '<span class="plus-minus" id="return_line_attachments_' . $row->getUniqueId() . '">+</span>';
        return $html;
    }

}
