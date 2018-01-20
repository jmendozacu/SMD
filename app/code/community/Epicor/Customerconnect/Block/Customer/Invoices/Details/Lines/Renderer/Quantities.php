<?php

/**
 * Currency display, converts a row value to currency display
 *
 * @author Sean Flynn
 */
class Epicor_Customerconnect_Block_Customer_Invoices_Details_Lines_Renderer_Quantities extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $list = array('Ordered' => 'quantities', 'Shipped' => 'delivered', 'To Follow' => 'to_follow');
        $quantitiesList = explode(',', Mage::getStoreConfig('customerconnect_enabled_messages/CUID_request/quantity_options'));
        $html = '';
        foreach ($quantitiesList as $quantity) {
            if ($row[$quantity]) {
                $html .= array_search($quantity, $list) . ' : ' . floatval($row[$quantity]) . '<br/>';
            } else {
                $html .= array_search($quantity, $list) . ' : ' . '<br/>';
            }
        }

        return $html;
    }

}
