<?php

/**
 * Block renderer class for Ship status  mapping checkbox field(is_deafult attribute)
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    /* is default attribute should be read only and it should not be editable from grid */

    public function render(Varien_Object $row) {
        $html = '';
        $is_default = $row->getIsDefault();
        $checked = ($row->getIsDefault()) ? 'checked' : '';
        if ($is_default) {
            $html .= '<input type="checkbox" disabled readonly value=' . "'" . $row->getIsDefault() . "'" . "checked=" . $checked . "></input>";
        } else {
            $html .= '<input type="checkbox" disabled readonly value=' . "'" . $row->getIsDefault() . "'" . "></input>";
        }
        return $html;
    }

}
