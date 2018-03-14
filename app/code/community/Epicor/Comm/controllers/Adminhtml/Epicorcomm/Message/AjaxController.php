<?php

class Epicor_Comm_Adminhtml_Epicorcomm_Message_AjaxController extends Epicor_Comm_Controller_Adminhtml_Abstract {
    
    public function networktestAction() {
        
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */
        $result = $helper->connectionTest();

        Mage::app()->getResponse()->setBody($result);
    }

    private function deleteCpn() {
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('epicor_comm/customer_sku')->load($id);
        if ($model->getId()) {
            try {
                $sku = $model->getSku();
                $model->delete();
                Mage::getSingleton('core/session')->addSuccess("Customer SKU : $sku deleted.");
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('Failed to delete Customer SKU');
            }
        }
    }

    public function deletecpncustomerAction() {

        $this->_redirect('adminhtml/customer_group/edit/', array(
            'id' => $this->getRequest()->getParam('customer', null)
                )
        );
    }

    public function deletecpnproductAction() {
        $this->deleteCpn();
        $this->_redirect('adminhtml/catalog_product/edit/', array(
            'id' => $this->getRequest()->getParam('product', null)
                )
        );
    }

    public function syncftpimagesAction() {
        $productId = $this->getRequest()->getParam('product', null);
        
        $helper = Mage::helper('epicor_comm/product_image_sync');
        /* @var $helper Epicor_Comm_Helper_Product */
        $helper->processErpImages($productId,true);
        
        Mage::app()->getResponse()->setBody('true');
    }

    /**
     * Processes Images Sync from Erp to Magento for a specific category
     */
    public function synccategoryimagesAction()
    {
        $categoryId = $this->getRequest()->getParam('category');
        
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            /* @var $category Mage_Catalog_Model_Category */
            if (!$category->isObjectNew()) {
                $helper = Mage::helper('epicor_comm/catalog_category_image_sync');
                /* @var $helper Epicor_Comm_Helper_Catalog_Category_image_sync */
                $helper->processErpImages($category, true);
            }
        }
        
        Mage::app()->getResponse()->setBody('true');
    }
    
    /**
     * Gets the p21 token via curl
     */
    public function p21tokenAction() {
        $helper = Mage::helper('epicor_comm/messaging');
        /* @var $helper Epicor_Comm_Helper_Messaging */

        Mage::app()->getResponse()->setBody($helper->getP21Token());
    }
    
    /**
     * Syncs Related Documents of product from ERP to Magento
     */
    public function syncRelatedDocsAction()
    {
        $productId = $this->getRequest()->getParam('product', null);
        
        $helper = Mage::helper('epicor_comm/product_relateddocuments_sync');
        /* @var $helper Epicor_Comm_Helper_Product_Relateddocuments_Sync */
        
        $helper->processRelatedDocuments($productId,true);
        
        Mage::app()->getResponse()->setBody('true');
    }
}


