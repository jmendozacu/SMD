<?php

class Epicor_Customerconnect_Block_Customer_Skus_Form extends Mage_Core_Block_Template {//Epicor_Common_Block_Generic_List {
    
    private $_sku;
    
    private $_product;
    
    public function getCustomerSku(){
        if (!$this->_sku){
            if(Mage::registry('sku')){
                $this->_sku = Mage::registry('sku');
            } else{
                $this->_sku = Mage::getModel('customerconnect/erp_customer_skus');
            }
        }
        return $this->_sku;
    }
    
    public function getProduct(){
        if(!$this->_product){
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }
    
    public function getBackUrl(){
        return $this->getUrl('*/*');
    }
    
    public function getSaveUrl(){
        return $this->getUrl('*/*/save');
    }
}
