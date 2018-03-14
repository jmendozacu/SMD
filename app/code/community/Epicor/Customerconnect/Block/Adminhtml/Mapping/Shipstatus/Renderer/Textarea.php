<?php

/**
 * Block renderer class for Ship status  mapping textarea field
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus_Renderer_Textarea extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {

    public function render(Varien_Object $row) {
        $html = '';

        $html .= '<textarea name="textarea_' . $row->getId() . '" readonly="true" rows="5" cols="50">' . $row->getStatusHelp() . '</textarea>';

        return $html;
    }

}
