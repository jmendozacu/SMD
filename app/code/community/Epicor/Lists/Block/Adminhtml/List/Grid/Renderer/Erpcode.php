<?php

/**
 * List ERP Code renderer
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Grid_Renderer_Erpcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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

        if ($row->getType() == 'Co') {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $accountCode = $helper->getSku($row->getData('erp_code'));
            $contractCode = $helper->getUom($row->getData('erp_code'));
            return $contractCode . ' - ' . $accountCode;
        } else {
            return $row->getErpCode();
        }
    }

}
