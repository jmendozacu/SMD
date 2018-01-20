<?php

class Epicor_FlexiTheme_Adminhtml_Flexitheme_ScopeController extends Epicor_Comm_Controller_Adminhtml_Abstract {
    protected $_action;
    protected $_actionType;
    protected $_renamedActionType;
    protected $_configCollection;
    public function indexAction() {     
       $previousUrlController = $this->getRequest()->getParam('previousUrlController');
       if($previousUrlController == 'flexitheme_layout'){
            Mage::register('action_type','layout');
        }else{
            Mage::register('action_type','skin');          
        }
        $this->loadLayout();
        $this->renderLayout();
    }
   
    public function activateSelectedStoresAction(){
        $this->_action = 'activate';
        $this->commonDataBaseActions();
    }
  
    public function deactivateSelectedStoresAction(){
        $this->_action = 'deactivate';
        $this->commonDataBaseActions();
    }
    public function commonDataBaseActions(){
        
        $layoutOrSkin = trim($this->getRequest()->getParam('layoutOrSkin')); 
        $this->_actionType = $this->getRequest()->getParam('actionType');    
        
        $this->_renamedActionType = $this->_actionType == 'skin'? 'theme':'layout';
        $this->_configCollection = Mage::getModel('core/config');
        // create loop to go through all selected data
        foreach($this->getRequest()->getParam('scope_checkbox') as $storeId){
            $store = Mage::getModel( "core/store" )->load($storeId);
            if ($this->_action == 'activate') {
                try {
                    $this->_configCollection->saveConfig("design/theme/{$this->_actionType}",
                                            Mage::helper("flexitheme/{$this->_renamedActionType}")->safeString($layoutOrSkin),
                                            'stores',
                                            $storeId);
                    Mage::getSingleton('core/session')->addSuccess($layoutOrSkin . Mage::helper('flexitheme')->__(' activated for store: ') . $store->getName());
                    $this->setFlexithemeAsPackage($store);
                    $this->updateTransactionEmailLogo($layoutOrSkin, $store);  
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError(
                      Mage::helper('flexitheme')->__('Unable to activate ')
                    . $layoutOrSkin
                    . Mage::helper('flexitheme')->__(' for store: ')
                    . $store->getName()
                    . Mage::helper('flexitheme')->__('. Please try again later'));
                }
            }else{
                // action is deactivate
                try{
                    $this->_configCollection->deleteConfig("design/theme/{$this->_actionType}",
                                        'stores',
                                        $storeId);
                    $this->updateTransactionEmailLogo($layoutOrSkin, $store);                    
                    Mage::getSingleton('core/session')->addSuccess($layoutOrSkin.Mage::helper('flexitheme')->__(' deactivated for store: ').$store->getName());
                }catch(Exception $e){
                    Mage::getSingleton('core/session')->addError(
                    Mage::helper('flexitheme')->__('Unable to deactivate ')
                    .$layoutOrSkin
                    .Mage::helper('flexitheme')->__(' for store: ')
                    .$store->getName()
                    .Mage::helper('flexitheme')->__('. Please try again later'));
                }
            }
        }
        if($this->_renamedActionType == 'layout'){
            $layout_id = Mage::getModel('flexitheme/layout')->load($layoutOrSkin,'layout_name')->getEntityId();
   //         if($layout_id){
                Mage::helper('flexitheme/layout')->setActiveLayout($layout_id, $storeId);
  //          }    
        }
        Mage::app()->cleanCache(array('CONFIG', 'LAYOUT_GENERAL_CACHE_TAG'));
        session_write_close();
        $this->_redirect("*/flexitheme_{$this->_renamedActionType}/index");
    } 
    public function setFlexithemeAsPackage($store){ 
          try {   
            $this->_configCollection->saveConfig('design/package/name',
                                    'flexitheme',
                                    'stores',
                                    $store->getStoreId());
         } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(
              Mage::helper('flexitheme')->__('Unable to set Flexitheme as package for store: ')            
            . $store->getName()
            . Mage::helper('flexitheme')->__('. Please try again later'));
         }
        
    }
    public function updateTransactionEmailLogo($layoutOrSkin, $store) {
        if ($this->_actionType == 'skin') {

            $skin = Mage::helper("flexitheme/{$this->_renamedActionType}")->safeString($layoutOrSkin);
            $skinLogoLocation = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_SKIN) . DS . "frontend" . DS . "flexitheme" . DS . $skin . DS . "images" . DS . "logo_email.gif";
             if (file_exists($skinLogoLocation)) {

                if ($this->_action == 'activate') {
                    try {
                        $newLogoLocation = Mage::getBaseDir() . DS . "media" . DS . "email" . DS . "logo" . DS . "stores" . DS . $store->getId();
                        if(!is_dir($newLogoLocation)){
                             mkdir($newLogoLocation, 755, true);
                        }
                        
                        $this->_configCollection->saveConfig("design/email/logo", "stores/". $store->getId()."/logo_email.gif", 'stores', $store->getId());
                        copy($skinLogoLocation, $newLogoLocation.DS."logo_email.gif");
                        Mage::getSingleton('core/session')->addSuccess($layoutOrSkin . Mage::helper('flexitheme')->__(' Transaction Logo activated for store: ') . $store->getName());
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError(
                                Mage::helper('flexitheme')->__('Unable to activate Transaction Logo for store: ')
                                . $store->getName()
                                . Mage::helper('flexitheme')->__('. Please try again later'));
                    }
                } else {
                    // remove flexitheme entry from core_config_data
                    try {
                        $this->_configCollection->deleteConfig("design/email/logo", 'stores', $store->getId());                        Mage::getSingleton('core/session')->addSuccess($layoutOrSkin . Mage::helper('flexitheme')->__(' Transaction Email Logo removed for store: ') . $store->getName());
                    } catch (Exception $e) {
                        Mage::getSingleton('core/session')->addError(
                                Mage::helper('flexitheme')->__('Unable to remove Transaction Logo from  ')
                                . $layoutOrSkin
                                . Mage::helper('flexitheme')->__(' for store: ')
                                . $store->getName()
                                . Mage::helper('flexitheme')->__('. Please try again later'));
                    }
                }
            }
        }
    }
}
