<?php

/**
 * Column Renderer for Contract Select Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Select_Grid_Renderer_Erpcode extends Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Action
{

    /**
     * render erp code
     * @param Epicor_Lists_Model_List $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Lists_Model_List */
        // needed so that contract ERP Codes render without delimiter

        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $contractCode = $helper->getUom($row->getData('erp_code'));
        return $contractCode;
    }

}
