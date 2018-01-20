<?php

/**
 * Order Reorder link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Dashboard_Orders_Renderer_Reorder
        extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {

        $html = '';

        $id = $row->getId();

        if (!empty($id)) {

            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */

            $return = Mage::getUrl('customerconnect/orders/');

            $html = '<a href="' . $helper->getOrderReorderUrl($row, $return) . '">' . $this->__('Reorder') . '</a>';
        }

        return $html;
    }

}
