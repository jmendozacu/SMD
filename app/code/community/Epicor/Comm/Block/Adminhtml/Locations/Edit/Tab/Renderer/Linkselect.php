<?php

/**
 * Entity register log details renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Renderer_Linkselect extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    private $_typesMap = array(
        Epicor_Comm_Model_Location_Link::LINK_TYPE_EXCLUDE => 'Included',
        Epicor_Comm_Model_Location_Link::LINK_TYPE_INCLUDE => 'Excluded',
        'N' => 'No Restriction',
    );

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Erpaccount */
        $value = $row->getLinkType() ?: 'N';

        $html = '<select name="' . $this->getColumn()->getFormFieldName() . '[' . $row->getEntityId() . ']">';

        foreach ($this->_typesMap as $code => $label) {
            $selected = ($code == $value) ? ' selected="selected"' : '';
            $html .= '<option value="' . $code . '"' . $selected . '>' . $this->__($label) . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

}
