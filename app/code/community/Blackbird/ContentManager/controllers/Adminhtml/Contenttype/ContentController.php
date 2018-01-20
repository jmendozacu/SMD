<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Adminhtml_ContentType_ContentController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('blackbird');
        return $this;
    }
    
    protected function _initContentType()
    {
        $this->_title($this->__('Content Type'))
        ->_title($this->__('Manage contents'));
    
        $contentTypeId  = (int) $this->getRequest()->getParam('ct_id');
        //if(!$contentTypeId) $contentTypeId = (int) $this->getRequest()->getParam('ct_id');
        $contentTypeModel = Mage::getModel('contentmanager/contenttype')
        ->setStoreId($this->getRequest()->getParam('store', 0));
      
        $contentTypeModel->setData('_edit_mode', true);
        if ($contentTypeId) {
            try {
                $contentTypeModel->load($contentTypeId);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        
        Mage::register('contentmanager', $contentTypeModel);
        Mage::register('current_contenttype', $contentTypeModel);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $contentTypeModel;
    }
   
    public function indexAction() {
    	   	
        $this->loadLayout();
        $this->_initAction();
        $this->_initContentType();
        
        $contentTypeModel = Mage::registry('current_contenttype');
        
        $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_content', 
            null, 
            array('_headerText' => Mage::helper('contentmanager')->__($contentTypeModel->getTitle()),
                  '_addButtonLabel' => Mage::helper('contentmanager')->__('Add').' '.$contentTypeModel->getTitle())
        ));
        $this->renderLayout();
    }
 
    public function editAction()
    {
        $this->_initContentType();
        $content_id     = $this->getRequest()->getParam('id');
        $content  = Mage::getModel('contentmanager/content')
                        ->setStoreId($this->getRequest()->getParam('store', 0))
                        ->load($content_id);
 
        if ($content->getId() || $content_id == 0)
        {
            if(!Mage::helper('contentmanager')->isViewAllowed($this->getRequest()->getParam('store', 0), $this->getRequest()->getParam('ct_id')))
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Permission denied'));
                $this->_redirect('*/*', array('id' => $this->getRequest()->getParam('id'), 'ct_id' => $this->getRequest()->getParam('ct_id'), 'store' => $this->getRequest()->getParam('store', 0)));
                return;
            }

            Mage::register('content_data', $content);
 
            $this->loadLayout();
            $this->_setActiveMenu('contentmanager/items');
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('contentmanager/adminhtml_content_edit'))
                 ->_addLeft($this->getLayout()->createBlock('contentmanager/adminhtml_content_edit_tabs'));
               
            $this->renderLayout();
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('contentmanager')->__('Content does not exist'));
            $this->_redirect('*/*/');
        }
    }
   
    public function newAction()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $ctId = $this->getRequest()->getParam('ct_id', 0);
        if(!Mage::helper('contentmanager')->isEditAllowed($storeId, $ctId))
        {
            //no permission -> check for first store
            $stores = Mage::getModel('core/store')->getCollection();
            foreach($stores as $store)
            {
                if(Mage::helper('contentmanager')->isEditAllowed($store->getId(), $ctId))
                {
                    $this->getRequest()->setParam('store', $store->getId());
                }
            }
        }
        $this->_forward('edit');
    }
   
    public function saveAction()
    {
    	if ($data = $this->getRequest()->getParams()) {
            try {
                //init model and set data
                $content = Mage::getModel('contentmanager/content');

                $storeId = ($this->getRequest()->getParam('store_id'))?$this->getRequest()->getParam('store_id'):0;
                $ct_id = $content->getCtId()?$content->getCtId():$this->getRequest()->getParam('ct_id');
                
                //check if some params are set (avoid admin login redirect with no params)
                if(!$this->getRequest()->getParam('url_key'))
                {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('You have been disconnected while you were editing this content. Ask your administrator to increase the session duration.'));
                    $this->_redirect('*/*/edit/', array('id' => $this->getRequest()->getParam('id'), 'ct_id' => $ct_id, 'store' => $storeId));
                    return;
                }
                
                //load existing model
                if ($id = $this->getRequest()->getParam('id')) {
                    $content->load($id);
                }
                
                //check permissions
                if(!Mage::helper('contentmanager')->isEditAllowed($storeId, $ct_id))
                {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Permission denied'));
                    $this->_redirect('*/*/edit/', array('id' => $this->getRequest()->getParam('id'), 'ct_id' => $ct_id, 'store' => $storeId));
                    return;
                }
                
                //get all existing attributes for the entity
                foreach($content->getAttributes() as $attribute)
                {
                    $attributes[$attribute->getAttributeCode()] = null;
                }
                
                $this->_initContentType();

                // Handle file management
                $data_file = $this->_uploadFile();
                foreach($data_file as $oneFile)
                {
                    $data = array_merge($oneFile, $data);
                }
                
                // file replacement
                foreach($data_file as $oneFile)
                {
                    foreach($oneFile as $fieldIdentifier => $fileName){
                        if(isset($data[$fieldIdentifier]) && $data[$fieldIdentifier] != $fileName){
                            $data[$fieldIdentifier] = $oneFile[$fieldIdentifier];
                        }
                    }
                }
                
                // file deletion
                if(is_array($this->getRequest()->getParam('delete')))
                {
                    foreach($this->getRequest()->getParam('delete') as $fieldIdentifier){
                        unset($data[$fieldIdentifier]);
                    }
                }
                
                //manage crop for images
                foreach($data as $key => $oneData)
                {
                    if(strpos($key, '_ctdi') !== false && isset($data[str_replace('_ctdi', '', $key)]))
                    {
                        $this->_doCrop($data[str_replace('_ctdi', '', $key)], $oneData, str_replace('_ctdi', '', $key));
                    }
                }
                
                //set generic data
                $content->setData($data);

                //empty data (for checkbox and multiple, set to null in oder to remove from DB, otherwise, attribute is keeped)
                $noResetFields = array('created_at');
                if(isset($attributes) && is_array($attributes))
                {
                    foreach($attributes as $key => $oneData)
                    {
                        if(!array_key_exists($key, $data) && !in_array($key, $noResetFields))
                        {
                            $content->setData($key, null);
                        }
                    }
                }
                
                //check for "multiple" values
                foreach($content->getData() as $key => $data)
                {
                    if(is_array($data) && $key != 'nodes')
                    {
                        $content->setData($key, implode(',', $data));
                    }
                }
                
                //Handle replacement patterns
                $this->_handlePatterns($content);

                //set entity id and update time
                if($id)
                {
                    $content->setEntityId($id);
                    $content->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
                }
                else
                {
                    $content->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
                }
                
                //clean image caches
                $this->_cleanImageCache($content);
                
                //save content
                $content->setStoreId($content->getStoreId());
                $content->save();
                
                //save content
                $this->_saveNodes();
               
                //set success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Content was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setContentData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $content->getId(), 'ct_id' => $content->getCtId(), 'store' => $content->getStoreId()));
                    return;
                }
                
                $this->_redirect('*/*/', array('ct_id' => $content->getCtId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setContentData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'ct_id' => $content->getCtId()));
                return;
            }
        }
        $this->_redirect('*/*/', array('ct_id' => $content->getCtId()));
    }
   
    /**
     * Delete a content entity
     * @return type
     */
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $content = Mage::getModel('contentmanager/content');
               
                $content->setId($this->getRequest()->getParam('id'))->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Content was successfully deleted'));
                $this->_redirect('*/*/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
            }
        }
        
        $this->_redirect('*/*/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
    }
    
    /**
     * Duplicate a content entity
     * @return type
     */
    public function duplicateAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $content = Mage::getModel('contentmanager/content')->setStoreId($this->getRequest()->getParam('store_id', 0))->load($this->getRequest()->getParam('id'));
                $content->setData('entity_id', null);
                
                //modify status and title
                $content->setStatus(0);
                $content->setTitle($content->getTitle().' ('.Mage::helper('contentmanager')->__('copy').')');
                
                //save new content
                $content->save();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Content was successfully deleted'));
                $this->_redirect('*/*/edit', array('id' => $content->getId(), 'store' => $this->getRequest()->getParam('store_id'), 'ct_id' => $this->getRequest()->getParam('ct_id')));
                
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
            }
        }
        
        $this->_redirect('*/*/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
    }    
    
   /**
    * Delete all attributes value for a specified storeId
    * @return type
    */
    public function deletestoreAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $content = Mage::getModel('contentmanager/content');
                $content->setStoreId($this->getRequest()->getParam('store_id'))->load($this->getRequest()->getParam('id'));
                $content->deleteCurrentStoreLink();
                $content->deleteCurrentStoreAttributes();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Content was successfully deleted'));
                $this->_redirect('*/*/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('ct_id' => $this->getRequest()->getParam('ct_id')));
            }
        }
        
        $this->_redirect('*/*/', array('ct_id' => $this->getRequest()->getParam('ct_id')));
    }
    
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('importedit/adminhtml_content_grid')->toHtml()
        );
    }
    
    /**
     * Get options fieldset block
     *
     */
    public function optionsAction()
    {
        $this->_initContentType();
        $this->loadLayout();
        $this->renderLayout();
    }

    private function _uploadFile(){
        $results = array();
        $helper = Mage::helper('contentmanager');
        
        foreach($_FILES as $identifier => $dataFile){
            if(isset($_FILES[$identifier]['name']) && $_FILES[$identifier]['name'] != '') {

                if(file_exists($_FILES[$identifier]['tmp_name'])){
                                       
                    $option = Mage::helper('contentmanager')->getOptionByFieldIdentifier($identifier);
                        
                    $allowedExtensions = explode(",", $option->getFileExtension());
                    $path = Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, $option->getFilePath());
                    
                    try {
                        $uploader = new Varien_File_Uploader($identifier);
                        $uploader->setAllowedExtensions($allowedExtensions);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);
                        try{
                            $dataimage = $uploader->save($path, $_FILES[$identifier]['name']);
                            $dataFileName = $dataimage['file'];
                            
                            $results[] = array($identifier => $dataFileName);
                        }
                        catch(Exception $e){
                            Mage::getSingleton('core/session')->addWarning($e->getMessage());
                            $results[] = array();
                        }
                    }
                    catch(Exception $e) {
                        Mage::log($e->getMessage());
                    }
                }
                else{
                    throw new Mage_Core_Exception("File ".$identifer." problem upload");
                }
            }
        } 
        return $results;
    }
    
    private function _doCrop($imageName, $dimensions, $identifier)
    {
        try {
            $helper = Mage::helper('contentmanager');
            $option = Mage::helper('contentmanager')->getOptionByFieldIdentifier($identifier);
            $dimensions = explode(':', $dimensions);
            
            $imagePathFull = str_replace(array("/", '\\'), DS, Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, $option->getFilePath()) . DS . $imageName);
            $imageCroppedPathFull = str_replace(array("/", '\\'), DS, Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, $option->getFilePath()) . DS . $helper->getCtImageCroppedFolder() . DS . $imageName);
            
            
            if(file_exists($imagePathFull))
            {
                if(!$dimensions[4] || !$dimensions[5])
                {
                    $dimensionsOrignial = getimagesize($imagePathFull);
                    $dimensions[4] = $dimensionsOrignial[0];
                    $dimensions[5] = $dimensionsOrignial[1];
                }
                
                $image = new Blackbird_ContentManager_Model_Image($imagePathFull);
                $image->crop($dimensions[1], $dimensions[0], $dimensions[4]-$dimensions[2], $dimensions[5]-$dimensions[3]);
                $image->quality(92);
                $image->save($imageCroppedPathFull);
            }
            
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }
    
    /**
     * Handle patterns replacement
     * {{news_title}} will be replaced by content of the corresponding fields
     * {{news_title|plain}} is used for a plain text value
     * @param type $content
     */
    private function _handlePatterns(&$content)
    {
        foreach($content->getData() as $attribute => $data)
        {
            $data = Mage::helper('contentmanager')->applyPattern($data, $content);
            $content->setData($attribute, $data);
        }
    }
    

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        $contentTypeId  = (int) $this->getRequest()->getParam('ct_id');
        
        $allowed = Mage::helper('contentmanager')->isViewAllowed($this->getRequest()->getParam('store', 0), $contentTypeId);
        if(!$allowed)
        {
            $stores = Mage::helper('contentmanager')->getStores();
            foreach($stores as $store)
            {
                $allowed = Mage::helper('contentmanager')->isViewAllowed($store->getId(), $contentTypeId);
                if($allowed) break;
            }
        }
        return $allowed;
    }
    
    private function _cleanImageCache($content)
    {
        $data = $content->getData();
        foreach($data as $key => $oneData)
        {
            if(strpos($key, '_ctdi') !== false && isset($data[str_replace('_ctdi', '', $key)]))
            {
                $imageName = $data[str_replace('_ctdi', '', $key)];
                if($imageName)
                {
                    $this->_cleanImageCacheByName($imageName);
                }
            }
        }
    }
    
    private function _cleanImageCacheByName($imageName)
    {
        $helper = Mage::helper('contentmanager');
        
        $folder = Mage::getBaseDir('media') . DS . $helper->getCtImageFolder();
        foreach($this->_findAllFiles($folder) as $dir)
        {
            $this->_emptyFolder($dir, $imageName);
        }
    }
    
    private function _findAllFiles($dir) 
    { 
        $root = scandir($dir); 
        $result = array();
        foreach($root as $value) 
        {
            if($value === '.' || $value === '..') {continue;} 
            if(is_dir("$dir/$value") && $value == 'cache') {$result[]="$dir/$value";continue;}
            if(is_file("$dir/$value")) { continue; }
            foreach($this->_findAllFiles("$dir/$value") as $value) 
            { 
                $result[]=$value; 
            }
        } 
        
        return $result; 
    }
    
    private function _emptyFolder($folder, $imageName)
    {
        $open=@opendir($folder);
        if (!$open) return;
        while($file=readdir($open)) {
                if ($file == '.' || $file == '..') continue;
                        if (is_dir($folder. DS .$file)) {
                                $r=$this->_emptyFolder($folder. DS .$file, $imageName);
                                if (!$r) return false;
                        }
                        else {
                            if($imageName == $file)
                            {
                                $r=@unlink($folder. DS .$file);
                                if (!$r) return false;
                            }
                        }
        }
        closedir($open);
        
        if (!isset($r)) return false;
            return true;
    }
    
    /**
     * Save all menu nodes modifications
     * @param type $content
     */
    private function _saveNodes()
    {
        $data = $this->getRequest()->getParams();
        if(isset($data['nodes']))
        {
            foreach($data['nodes'] as $nodeId => $params)
            {
                $node = Mage::getModel('contentmanager/menu_node')->load($nodeId);
                $node->setUrlPath($params['url_path']);
                $node->setCanonical($params['canonical']);
                
                $node->save();
            }
        }
    }
}