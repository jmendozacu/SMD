<?php

/**
 * Contract Delivery Address Reorder link grid renderer
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_List_Renderer_DeliveryAddress extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    public function render(Varien_Object $row)
    {

        $html = '';

        $id = $row->getId();
        if (!empty($id)) {
            $addresses = Mage::helper('epicor_common/xml')->varienToArray($row->getDeliveryAddressesDeliveryAddress());
            /* @var $helper Epicor_Common_Helper_Xml */

            // this removes fields that are not required from the address array (if only a single address, it is a string and all fields are required) 
            if (is_array($addresses)) {
                unset($addresses[0]['address_code']);
                unset($addresses[0]['purchase_order_number']);
                unset($addresses[0]['name']);
                unset($addresses[0]['telephone_number']);
                unset($addresses[0]['fax_number']);
                unset($addresses[0]['email_address']);
                $html = implode(',', $addresses[0]);
            } else {
                $html = $addresses;
            }
        }

        return $html;
    }

}
