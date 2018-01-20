<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Sync extends Mage_Adminhtml_Block_Template{
    public function getAjaxUrl()
    {
        $product = Mage::registry('current_product');
        $params = array('product'=>$product->getId());
        return $this->getUrl('adminhtml/epicorcomm_message_ajax/syncftpimages',$params);
    }
    
    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', array(
            '_current'   => true,
            'tab'        => 'product_info_tabs_group_10',
            'active_tab' => null
        ));
    }
}

