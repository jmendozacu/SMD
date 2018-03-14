<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Common_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu
{

    public function getCacheKeyInfo()
    {
        $shortCacheId = array(
            'TOPMENU',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getCurrentEntityKey()
        );
        
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $extraKeys = $helper->getCategoryCacheKeys();
        if (is_array($extraKeys)) {
            $shortCacheId = array_merge($shortCacheId, $extraKeys);
        }
        
        $cacheId = $shortCacheId;
        
        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['entity_key'] = $this->getCurrentEntityKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    private function hasAccess()
    {
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        return $accessHelper->canAccessUrl('catalog/category/view');
    }

    protected function _toHtml()
    {
        $html = '';

        if ($this->hasAccess()) {
            $html = parent::_toHtml();
        }

        return $html;
    }

}
