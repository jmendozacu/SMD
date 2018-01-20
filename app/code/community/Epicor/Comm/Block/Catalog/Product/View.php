<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View 
{
    public function getProduct()
    {
        $product = parent::getProduct();
        if(Mage::app()->getRequest()->getParam('qty', false)) {
            $product->setPreconfiguredValues(
                    $product->getPreconfiguredValues()
                    ->setQty(
                            Mage::app()->getRequest()->getParam('qty')
                            )
                    );
        }
        return $product;
    }
    
    public function getSkuEditUrl($entityId){
        return $this->getUrl('customerconnect/skus/edit', array('id' => $entityId));
    }
    
    public function getSkuAddUrl($productId){
        return $this->getUrl('customerconnect/skus/create', array('id' => $productId));
    }
    
    /**
     * Return price block
     *
     * @param string $productTypeId
     * @return mixed
     */
    public function _getPriceBlock($productTypeId)
    {
        return parent::_getPriceBlock($productTypeId);
    }
}
