<?php

/**
 * Column Renderer for Sales ERP Account Select Grid Address
 *
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Address extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{

    public function renderAddress(Varien_Object $row, $type)
    {
        $addressFields = array('name', 'address1', 'address2', 'address3', 'city', 'county', 'country', 'postcode');
        $glue = '';
        $text = '';
        foreach ($addressFields as $field) {
            $fieldData = trim($row->getData($type . '_' . $field));
            if ($fieldData && !empty($fieldData)) {
                $text .= $glue . $fieldData;
                $glue = ', ';
            }
        }

        return $text;
    }

}
