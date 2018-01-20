<?php

class Epicor_Customerconnect_SkusController extends Epicor_Customerconnect_Controller_Abstract {
    
        
    private function canCustomerAccessEditingSkus(){
        
        $canCustomerEditSkus = Mage::helper('customerconnect/skus')->canCustomerEditCpns();
        /* @var $canCustomerEditSkus Epicor_Customerconnect_Helper_Skus */
        
        if(!$canCustomerEditSkus){
            Mage::getSingleton('core/session')->addError($this->__('You do not have permission to access this page'));
            $this->_redirectReferer();
        }
        
    }

    public function indexAction() {
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
        
    }

    public function createAction() {
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        
        $this->canCustomerAccessEditingSkus();
        
        try {
            $productID = $this->getRequest()->get('id');

//            $commHelper = Mage::helper('epicor_comm');
//            /* @var $commHelper Epicor_Comm_Helper_Data */
//            $customerGroupId = $commHelper->getErpAccountInfo()->getId();
//
//            $sku = Mage::getModel('customerconnect/erp_customer_skus')
//                    ->getCollection()
//                    ->addFieldToFilter('customer_group_id', $customerGroupId)
//                    ->addFieldToFilter('product_id', $productID)
//                    ->getFirstItem();
//
//            if (is_null($sku->getEntityId())) {
                $product = Mage::getModel('catalog/product')->load($productID);
                if (is_null($product->getId())) {
                    throw new Exception('Invalid product');
                } else {
                    Mage::register('product', $product);
                    $this->loadLayout();
                    $this->renderLayout();
                }
//            } else {
//                $this->_redirect('*/*/edit', array('id' => $sku->getEntityId()));
//            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('Product not found'));
            $this->_redirectReferer();
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('Error trying to retrieve product'));
            $this->_redirectReferer();
        }
    }

    public function editAction() {
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        
        $this->canCustomerAccessEditingSkus();

        $errorMsg = $this->__('Error trying to retrieve SKU');

        try {
            $sku = Mage::getModel('customerconnect/erp_customer_skus')->load($this->getRequest()->get('id'));
            /* @var $sku Epicor_Customerconnect_Model_Quote */

            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccountInfo = $commHelper->getErpAccountInfo();

            if ($sku->getCustomerGroupId() == $erpAccountInfo->getId()) {

                Mage::register('sku', $sku);
                Mage::register('product', $sku->getProduct());

                $this->loadLayout();
                $this->renderLayout();
            } else {
                $errorMsg .= $this->__(': You do not have permission to access this SKU');
                throw new Exception('Invalid customer');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
            $this->_redirectReferer();
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
            $this->_redirectReferer();
        }
    }

    public function saveAction() {
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        
        $this->canCustomerAccessEditingSkus();

        $error = true;
        $errorMsg = $this->__('Error trying to save SKU');
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        

        try {

            $entityId = $this->getRequest()->getPost('entity_id');
            $productId = $this->getRequest()->getPost('product_id');
            $customerSku = $this->getRequest()->getPost('customer_sku');
            $description = $this->getRequest()->getPost('description');

            $customerGroupId = Mage::helper('epicor_comm')->getErpAccountInfo()->getId();

//            $duplicatedSku = Mage::getModel('customerconnect/erp_customer_skus');
//            
//            $duplicatedSku->getCollection()
//                    ->addFieldToFilter('customer_group_id', $customerGroupId)
//                    ->addFieldToFilter('sku', $customerSku)
//                    ->getFirstItem();
          
            
            $sku = Mage::getModel('customerconnect/erp_customer_skus');

            if(!$customerSku){
                $errorMsg .= $this->__(': SKU is a required field');
                throw new Exception('Invalid SKU');
                
//            }else if(($entityId && $duplicatedSku->getEntityId() && $duplicatedSku->getEntityId() != $entityId)
//                    || ($productId && $duplicatedSku->getEntity())){
//                $errorMsg .= $this->__(': There is another product with this SKU');
//                throw new Exception('Invalid SKU');
//                
            } else if ($entityId) {
                $sku->load($entityId);

                if ($sku->getEntityId() && $sku->getCustomerGroupId() == $customerGroupId) {
                    $error = false;
                } else {
                    $errorMsg .= $this->__(': The SKU was not found or you do not have permission to access this SKU');
                    throw new Exception('Invalid customer');
                }
                
            } else if($productId){
//                $sku->getCollection()
//                        ->addFieldToFilter('customer_group_id', $customerGroupId)
//                        ->addFieldToFilter('product_id', $productId)
//                        ->getFirstItem();
                
                $product = Mage::getModel('catalog/product')->load($productId);

                if ($productId && $product->getId()) {
                    $sku->setProductId($productId);
                    $sku->setCustomerGroupId($customerGroupId);

                    $error = false;
                } else {
                    $errorMsg .= $this->__(': The product does not exist');
                    throw new Exception('Invalid product');
                }
            }else{
                $errorMsg .= $this->__(': Either Product or Entity ID is needed');
            }
            
            if($error){
                throw new Exception('Unknown Error');
            }else{
                $sku->setSku($customerSku);
                $sku->setDescription($description);
                $sku->save();
            }
            
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('Error trying to retrieve product'));
        }
        
        if($error){
            $this->_redirectReferer();
        }else{
            Mage::getSingleton('core/session')->addSuccess($this->__('SKU was successfully saved'));
            $this->_redirect('*/*');
        }
        
    }

    public function deleteAction() {
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }
        
        $this->canCustomerAccessEditingSkus();

        $errorMsg = $this->__('Error trying to retrieve SKU');
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            return;
        }

        try {
            $sku = Mage::getModel('customerconnect/erp_customer_skus')->load($this->getRequest()->get('id'));
            /* @var $sku Epicor_Customerconnect_Model_Quote */

            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccountInfo = $commHelper->getErpAccountInfo();

            if ($sku->getCustomerGroupId() == $erpAccountInfo->getId()) {
                $sku->delete();

                Mage::getSingleton('core/session')->addSuccess($this->__('SKU was successfully deleted'));
                $this->_redirect('*/*');
            } else {
                $errorMsg .= $this->__(': You do not have permission to delete this SKU');
                throw new Exception('Invalid customer');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
            $this->_redirectReferer();
        } catch (Mage_Exception $e) {
            Mage::getSingleton('core/session')->addError($errorMsg);
            $this->_redirectReferer();
        }
    }
    
    /**
     * Export SKUs grid to CSV format
     */
    public function exportToCsvAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_skus.csv";
        $content = $this->getLayout()->createBlock('customerconnect/customer_skus_list_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export SKUs grid to XML format
     */
    public function exportToXmlAction() {
        $baseUrl = Mage::helper('customerconnect')->urlWithoutHttp();
        $fileName = "{$baseUrl}_skus.xml";
        $content = $this->getLayout()->createBlock('customerconnect/customer_skus_list_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

}
