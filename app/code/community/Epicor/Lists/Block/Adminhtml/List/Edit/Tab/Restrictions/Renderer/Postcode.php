<?php

/**
 * List Restricted postcode renderer
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Renderer_Postcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{

    public function render(Varien_Object $row)
    {
        $postcode = $row->getPostcode();
        $html = str_replace(array('.', '^', '$'), '', $postcode);
        return $html;
    }

}
