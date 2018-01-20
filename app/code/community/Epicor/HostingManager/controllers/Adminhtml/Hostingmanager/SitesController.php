<?php

/**
 * Sites admin controller
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Adminhtml_Hostingmanager_SitesController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function indexAction()
    {
        $this->_title($this->__('Sites'));

        $this->loadLayout();
        $this->_setActiveMenu('epicor_common/hostingmanager');
        $this->_addBreadcrumb(Mage::helper('customer')->__('Manage Hosting'), Mage::helper('customer')->__('Manage Hosting'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $site = $this->_loadSite($id);


        $this->_title($this->__('Sites'));
        if ($site->getId())
            $this->_title($this->__('Edit %s', $site->getName()));
        else
            $this->_title($this->__('Add Site'));

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        // if http: or https: submitted, return to main screen with error 
        $url = $this->getRequest()->getParam('url');
        $extraDomain = trim($this->getRequest()->getParam('extra_domains')); 
       
        
        if((stripos($url,'http://') !== false) || (stripos($url,'https://')) !== false)
        {
            Mage::getSingleton('adminhtml/session')->addError("Unable to save url with prefix of http:// or https://"); 
     //       $this->_redirect('epicor_hostingmanager/adminhtml_sites/index');
            $this->_redirect('*/*/');
        //    Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("epicor_hostingmanager/adminhtml_sites/index"));
        }else{
            
            //validate domain name
            $regex = "/^([a-z0-9][a-z0-9\-\.]{1,63})$/i";
            if (!preg_match($regex, $url, $matches)) {                
                $error = $this->__('Unable to save url %s as it contains invalid characters, it must only contain alphanumerics, dashes "-" and full stops "."', $url);
                Mage::getSingleton('adminhtml/session')->addError($error); 
                $this->_redirect('*/*/');
                return;
            }
            
            $hostingHelper = Mage::helper('hostingmanager');
            /* @var @helper Epicor_HostingManager_Helper_Data */
            $checkExtraDomain = $hostingHelper->checkExtraDomain($extraDomain);
            if($checkExtraDomain['status'] =="error") {
                $error = $this->__('Unable to save extra domain %s as it contains '. $checkExtraDomain['message'], $checkExtraDomain['data']);
                Mage::getSingleton('adminhtml/session')->addError($error); 
                $this->_redirect('*/*/');
                return;                
            }            
           
            if($data = $this->getRequest()->getPost()) {
                $id = $this->getRequest()->getParam('id', null);
                $site = $this->_loadSite($id);
                try {
                    $site->setName($data['name']);
                    $secure = isset($data['secure']) ? $data['secure'] : 0;
                    //Remove space after comma
                    $removeSpaces = preg_replace("/\s*,\s*/", ",", $data['extra_domains']);
                    $serverName = isset($data['extra_domains']) ? rtrim(trim($removeSpaces),',') : "";
                    $site->setExtraDomains($serverName);    
                    $site->setSecure($secure);                    
                    $site->setUrl(strtolower($data['url']));
                    if (!empty($data['certificate_id'])) {
                        $site->setCertificateId($data['certificate_id']);
                    } else {
                        $site->setCertificateId(null);
                    }

                    $doValidation = true;
                    if ($site->getIsDefault()) {
                        $doValidation = false;
                        $childId = 0;
                        $site->setCode('');
                        $site->setScope('default');
                    } elseif (strpos($data['child_id'], 'website') !== false) {
                        $childId = str_replace('website_', '', $data['child_id']);
                        $website = Mage::app()->getWebsite($childId);
                        if ($website) {
                            $site->setCode($website->getCode());
                            $site->setIsWebsite(true);
                            $website_store_id = $website->getDefaultStore()->getId();
                            $website_id = $website->getId();
                        }
                    } else {
                        $childId = str_replace('store_', '', $data['child_id']);
                        $store = Mage::app()->getStore($childId);
                        if ($store) {
                            $site->setCode($store->getCode());
                            $site->setIsWebsite(false);
                            $website = $store->getWebsite();
                            $website_store_id = $website->getDefaultStore()->getId();
                            $website_id = $website->getId();
                        }
                    }

                    $site->setChildId($childId);
                    if ($doValidation) {
                        $validation_error = array();
                        $url_site = Mage::getModel('hostingmanager/site')->load($data['url'], 'url');
                        /* @var $url_site Epicor_HostingManager_Model_Site */
                        if (
                                $url_site->getId() &&
                                $site->getId() != $url_site->getId()
                        ) {
                            $validation_error[] = Mage::helper('hostingmanager')->__('You can not have multiple sites with the same url');
                        }

                        $site_collection = Mage::getModel('hostingmanager/site')->getCollection();
                        /* @var $site_collection Epicor_HostingManager_Model_Resource_Site_Collection */


                        $site_collection
                                ->getSelect()
                                ->where('((child_id = ' . $website_id . ' && is_website = 1  && ' . $childId . ' = ' . $website_store_id . ')OR (child_id = ' . $childId . ' && is_website = 0))')
                                ->where('entity_id != ?', $site->getId());

    //                    $validation_error[] = $site_collection->getSelectSql(true);
    //                    $validation_error[] = Mage::app()->getDefaultStoreView()->getId()." == $childId && ".var_export(!$site->getIsWebsite(), true);
                        if ($site_collection->count()) {
                            //$validation_error[] = $site_collection->getSelectSql(true);
                            $validation_error[] = Mage::helper('hostingmanager')->__('You can not have multiple sites linking to the same store or website');
                        }

                        if((Mage::app()->getDefaultStoreView()->getId() == $childId && !$site->getIsWebsite()) || (Mage::app()->getDefaultStoreView()->getWebsite()->getId() == $childId && $site->getIsWebsite())) {
                            $validation_error[] = Mage::helper('hostingmanager')->__('Only the Default website can link to the default website and store view');
                        }

                        if ($validation_error) {
                            Mage::throwException(implode('<br>', $validation_error));
                        }
                    }
                    $site->save();
                    
                    $hostingHelper->setUnsecureHttps($site);

                    if (!$site->getId()) {
                        Mage::throwException(Mage::helper('hostingmanager')->__('Error saving Site'));
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('hostingmanager')->__('Site was successfully saved.'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    $this->_redirect('*/*/');
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    if ($site) {
                        Mage::unregister('current_site');
                        Mage::register('current_site', $site);
                        $this->_forward('edit');
                    } else {
                        $this->_redirect('*/*/');
                    }
                }

                return;
            }
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('No data found to save'));
            $this->_redirect('*/*/');
        }    
    }

    public function deleteAction()
    {

        $id = $this->getRequest()->getParam('id', null);
        $site = $this->_loadSite($id);

        if (!$site->getIsDefault()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('Site deleted'));
            $site->delete();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('Default Site can not be deleted'));
        }

        $this->_redirect('*/*/');
    }

    private function _loadSite($id)
    {

        $model = Mage::getModel('hostingmanager/site');
        /* @var $model Epicor_HostingManager_Model_Site */

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('hostingmanager')->__('Site does not exist'));
                $this->_redirect('*/*/sites');
            }
        }

        if (!Mage::registry('current_site')) {
            Mage::register('current_site', $model);
        }

        return $model;
    }

}
