<?php

/**
 * Return status renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Sales_Returns_Renderer_Rmadate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /* @var $row Epicor_Comm_Model_Customer_Return */
        return Mage::helper('core')->formatDate($row->getRmaDate(), 'medium', false);
    }

}
