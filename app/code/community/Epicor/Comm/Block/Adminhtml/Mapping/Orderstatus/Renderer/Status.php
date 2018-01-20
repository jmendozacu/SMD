<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Type
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Adminhtml_Mapping_Orderstatus_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $status = Mage::getSingleton('sales/order_status')->load($row['status'], 'status');
        if($status['label']){
            
            return $status['label'];
        }else{
            return 'No Matching Order Status';
        }    
    }
}
