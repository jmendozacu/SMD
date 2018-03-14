<?php

class Epicor_Comm_StoreController extends Mage_Core_Controller_Front_Action
{

    public function selectorAction()
    {
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */

        
     //   $stores = $helper->getBrandSelectStores();
        $stores = $helper->getSelectableStores();
        $storeCount = count($stores);
  
        switch ($storeCount) {
            case 0:
                Mage::getSingleton('core/session')->addError('No Stores available for this user on this site, unable to log in');
                $url = Mage::helper('customer')->getLogoutUrl();
                Mage::app()->getResponse()->setRedirect($url);  
                break;
            case 1:
                $storeId = array_keys($stores);
                $store_code = Mage::app()->getGroup($storeId[0]);
                
                $code = Mage::getModel('core/store')->load($store_code->getDefaultStoreId())->getCode();
                Mage::getSingleton('customer/session')->setHasStoreSelected(true);
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('', array('_query' => array('___store' => $code))));  
                break;
            default:
                $this->loadLayout();
                $this->renderLayout();
                break;
        }
        
        //--SF the original code is below, this can be removed if the above is ok
//        
//        if ($storeCount <= 1) {
//            if (!Mage::getSingleton('customer/session')->getHasStoreSelected()) {
//                Mage::getSingleton('customer/session')->setHasStoreSelected(true);
//            }
//            
//            //
//            $website = Mage::app()->getWebsite();
//            $store = $website->getDefaultStore();
//            Mage::app()->getResponse()->setRedirect(Mage::getUrl('', array('_query' => array('___store' => $store->getCode()))));
//            
////            Mage::getSingleton('core/session')->addError('No Stores available for this user on this site, unable to log in');
////            $url = Mage::helper('customer')->getLogoutUrl();
////            Mage::app()->getResponse()->setRedirect($url);  
//        } else {
//            $this->loadLayout();
//            $this->renderLayout();
//        }
    }

    public function selectAction()
    {
        $helper = Mage::helper('epicor_comm');
        $redirectHelper = Mage::helper('epicor_common/redirect');
        /* @var $helper Epicor_Comm_Helper_Data */
        $storeId = Mage::app()->getRequest()->getParam('store');
        
        $stores = $helper->getBrandSelectStores();
        $storeIds = array_keys($stores);
        $store = Mage::getModel('core/store_group')->load($storeId);
        
        if (in_array($storeId, $storeIds) && !$store->isObjectNew()) {
    
            $customerSession = Mage::getSingleton('customer/session');
            $customerSession->setHasStoreSelected(true);
            $view = $store->getDefaultStore();
            if($view){
                $path = '';
                $customer = $customerSession->getCustomer();
                /* @var $customer Epicor_Comm_Model_Customer */
                if($customer->isSupplier() || $customer->isSalesRep())
                {
                    $path = 'customer/account';
                }
                $url = Mage::getUrl($path, array('_query' => array('___store' => $view->getCode())));
            }else{
                Mage::getSingleton('core/session')->addError('Selected Store has no valid store view. Unable to continue');
                $url = Mage::getUrl('epicor_comm/store/selector');
            }    
        } else {
            Mage::getSingleton('customer/session')->setHasStoreSelected(false);
            Mage::getSingleton('core/session')->addError('Invalid Store Choice, Please Select a Valid Store');
            $url = Mage::getUrl('epicor_comm/store/selector');
        }
        
        Mage::app()->getResponse()->setRedirect($url);
    }

}
