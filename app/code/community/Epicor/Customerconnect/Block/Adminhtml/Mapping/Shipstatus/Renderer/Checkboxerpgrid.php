<?php

/**
 * Block renderer class for Ship status  mapping checkbox
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Shipstatus_Renderer_Checkboxerpgrid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    /* we need to make checkbox read only if it is default */

    public function render(Varien_Object $row) {
        $html = '';
        $erpAccount = Mage::registry('customer_erp_account');
        $shipCodesMapped = unserialize($erpAccount->getAllowedShipstatusMethods());
        $allowcheck = array();
        $allowed = ($shipCodesMapped) ? $shipCodesMapped : array();
        foreach ($allowed as $allow) {
            $allowcheck[] = $allow;
        }
        $is_default = $row->getIsDefault();
        $checked = ($is_default) ? 'checked' : '';
        $search = in_array($row->getCode(), $allowcheck);
        if ($search && $row->getIsDefault() != 1) {
            $check = 'checked';
            $html .= '<input type="checkbox" name=links[] value=' . "'" . $row->getCode() . "'" . "checked=" . $check . " class='checked'></input>";
            return $html;
        } elseif ($row->getIsDefault() == 1) {
            $html .= '<input name=links[] type="checkbox" disabled readonly value=' . "'" . $row->getCode() . "'" . "checked=" . $checked . " class='checked'></input>";
            return $html;
        } else {
            if ($is_default) {
                $html .= '<input name=links[] type="checkbox" disabled readonly value=' . "'" . $row->getCode() . "'" . "checked=" . $checked . " class='checked'></input>";
                return $html;
            } else {
                $html .= '<input name=links[] type="checkbox" class="checked" value=' . "'" . $row->getCode() . "'" . "></input>";
                return $html;
            }
        }
    }

}
