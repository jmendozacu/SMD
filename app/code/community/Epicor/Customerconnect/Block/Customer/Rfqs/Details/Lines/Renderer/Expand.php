<?php

/**
 * RFQ line row expand renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Lines_Renderer_Expand extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        if (Mage::getStoreConfig('customerconnect_enabled_messages/CRQD_request/attachment_support')) {
            $html = '<span class="plus-minus" id="attachments-' . $row->getUniqueId() . '">+</span>';
        } else {
            $html = '';
        }
        return $html;
    }

}
