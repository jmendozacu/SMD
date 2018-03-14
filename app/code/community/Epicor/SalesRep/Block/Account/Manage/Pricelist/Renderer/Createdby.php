<?php

/**
 * List Grid ownerid grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Renderer_Createdby extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{
    
    public function render(Varien_Object $row)
    {
        $html = '';
        $value =  $row->getData($this->getColumn()->getIndex());
        if (!empty($value)) {
            $Customer = Mage::getModel("customer/customer");
            $getCreatedBy = $Customer->load($value);    
            $emailId = $getCreatedBy->getEmail();            
            $html = $emailId;
        }
        return $html;        
    }
}