<?php

/**
 * Invoice status display, converts an erp invoice status code to mapped invoice status
 *
 * @author Gareth.James
 */
class Epicor_Customerconnect_Block_List_Renderer_Erpquotestatus
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $helper = Mage::helper('customerconnect/messaging');
        /* @var $helper Epicor_Customerconnect_Helper_Messaging */

        $index = $this->getColumn()->getIndex();
        return $helper->getErpquoteStatusDescription($row->getData($index));
    }

}
