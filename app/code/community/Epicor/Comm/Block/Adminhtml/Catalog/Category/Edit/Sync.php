<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Comm_Block_Adminhtml_Catalog_Category_Edit_Sync extends Mage_Adminhtml_Block_Template
{
    /**
     * Returns ajax url for images sync
     *
     * @return string $ajaxUrl
     */
    public function getAjaxUrl()
    {
        $category = Mage::registry('current_category');
        $params   = array('category' => $category->getId());
        $ajaxUrl  = $this->getUrl('adminhtml/epicorcomm_message_ajax/synccategoryimages', $params);
        
        return $ajaxUrl;
    }
    
    /**
     * Returns edit url once image sync is done
     *
     * @return string $editUrl
     */
    public function getEditUrl()
    {
        $category = Mage::registry('current_category');
        $params   = array('id' => $category->getId());
        $editUrl  = $this->getUrl('*/*/edit', $params);
        
        return $editUrl;
    }
}

