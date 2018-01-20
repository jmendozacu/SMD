<?php

/**
 * Entity register log details renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Renderer_Productenabler extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $selected = $row->getLocationCode() !== null ? 'checked="checked"' : '';
        
        $html = '<input name="' . $this->getColumn()->getName() . '[' . $row->getEntityId() . ']" type="checkbox" '.$selected.' onclick="return productLocations.rowEdit(this,'.$row->getEntityId().');return false;" />';

        return $html;
    }

}
