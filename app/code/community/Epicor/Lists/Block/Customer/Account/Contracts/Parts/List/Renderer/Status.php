<?php

/**
 * RFQ line attachments column renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Contracts_parts_List_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {  
        return Mage::helper('epicor_lists')->getStatus($row->getLineStatus());  
    }

}
