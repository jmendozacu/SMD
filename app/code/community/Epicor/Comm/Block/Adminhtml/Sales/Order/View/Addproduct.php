<?php


class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproduct extends Mage_Adminhtml_Block_Template
{
    public function getAddProductUrl() {
        return Mage::helper("adminhtml")->getUrl("adminhtml/epicorcomm_sales_order/addproduct/", array('order_id' => $this->getOrderId()));
    }
    
    public function getSaveProductUrl() {
        return Mage::helper("adminhtml")->getUrl("adminhtml/epicorcomm_sales_order/saveproducts/", array('order_id' => $this->getOrderId()));
    }
    
    public function getAddProductBtnVisability() {
        return Mage::getStoreConfigFlag('Epicor_Comm/payments/active') ? 'true' : 'false';
    }
    
    protected function getOrderId() {
        return $this->getRequest()->get('order_id');
    }
}