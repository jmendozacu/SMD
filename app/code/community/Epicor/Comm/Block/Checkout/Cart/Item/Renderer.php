<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Epicor_Comm_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{

    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl($isGroupedProduct = null, $params = null)
    {
//        $isGroupedProduct = $this->isGroupProduct($this->getItem()->getProduct());
//        if($isGroupedProduct){
//            Mage::register("grouped_product_{$this->getItem()->getId()}",$isGroupedProduct);
////            $this->getItem()->setId($isGroupedProduct);
//        }

        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $options = $helper->getCustomOptions($this->getItem());
        
        if(!is_array($options)) {
            $options = array();
        }
        
        $configurator = false;
        $canReConfigure = 'Y';
        foreach ($options as $option) {
            $option_type=isset($option['option_type'])?$option['option_type']:null;
            $option_value=isset($option['value'])?$option['value']:null;
            if ($option_type == 'ewa_code') {
                $configurator = $option_value;
                $productId = $this->getItem()->getProductId();
            } else if ($option_type == 'ewa_configurable') {
                $canReConfigure = $option_value;
            }
        }
        
        if($canReConfigure == 'N') {
            return false;
        }
        
        if ($configurator) {
            $currentStoreId = Mage::app()->getStore()->getStoreId();
            return "javascript: ewaProduct.edit({ewaCode: '$configurator',currentStoreId:'$currentStoreId', productId: '$productId', itemId: '{$this->getItem()->getId()}'}, false);";
        } elseif (is_array($params)) {
            $params['id'] = $this->getItem()->getId();
            return $this->getUrl('checkout/cart/configure', $params);
        } else {
            return parent::getConfigureUrl();
        }
    }

   public function isGroupProduct($product)
    {
        if ($product->getTypeId() == "simple") {
            $parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
            if (!empty($parentIds)) {
                $parentProduct = Mage::getModel('catalog/product')->getCollection()
                                    ->addAttributeToSelect('*')
                                    ->addAttributeToFilter('entity_id', array('in'=>$parentIds))
                                    ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', 1);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($parentProduct);
                if ($parentProduct->getSize() > 0){
                    return $parentProduct->getFirstItem()->getId();
                }
            }            
        }
    } 

    public function getProductUrlPath($id)
    {
        $product = Mage::getModel('catalog/product')->load($id);
        return $product->getUrlPath();
    }
    
    public function getProductOptions()
    {
       $productOptions = parent::getProductOptions();
        if($productOptions){
            $ewaHelper = Mage::helper('epicor_comm/configurator');
            /* @var $ewaHelper Epicor_Comm_Helper_Configurator */     
            $checkOptions = $ewaHelper->getEwaOptions($productOptions); 
            return $checkOptions;
        }
    }

    public function getQty()
    {
        if (!$this->_strictQtyMode && (string) $this->getItem()->getQty() == '') {
            return '';
        }
        $commHelper = Mage::helper('epicor_comm');
        $decimalPlaces = $commHelper->getDecimalPlaces(Mage::getResourceModel('catalog/product')->getAttributeRawValue($this->getItem()->getProduct()->getId(), 'decimal_places', Mage::app()->getStore()->getStoreId()));
        return $commHelper->truncateZero($this->getItem()->getQty() * 1, $decimalPlaces);
    }

}
