<?php

/**
 * Amend input types for epicor_comm/erp_mapping_attributes 
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Renderer_Inputtype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $options = Mage::helper('epicor_comm')->_getEccattributeTypes();
        $x = $row->getInputType();
        return $options[$row->getInputType()];
    
    }  

}
