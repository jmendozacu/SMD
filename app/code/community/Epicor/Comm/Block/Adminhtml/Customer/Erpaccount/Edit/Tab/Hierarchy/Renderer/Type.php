<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Hierarchy_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $type = $row->getData($this->getColumn()->getIndex());
        
        $types = Epicor_Comm_Model_Erp_Customer_Group_Hierarchy::$linkTypes;
        
        return isset($types[$type]) ? $types[$type] : $type;
    }

}
