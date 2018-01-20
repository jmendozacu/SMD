<?php

class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Contractquantities extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$list = array ('Min Order Qty'=>'min_order_qty', 'Max Order Qty'=>'max_order_qty', 'Contract Qty'=>'qty');
        $quantitiesList = array('min_order_qty','max_order_qty','qty');
        $html = '';
        foreach($quantitiesList as $quantity){
                $html .= array_search($quantity, $list).' : '.floatval($row[$quantity]).'<br/>';
        }
        
        return $html;
    }

}