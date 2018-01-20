<?php

/**
 * Common ImportExport controller
 *
 * This controls the import and export function in the admin
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 * when adding a table to  the array, the key values indicate what will be part of the addFieldToFilter parm
 * the Id value is the value of the table id (usually id or entity_id, but can be different)
 * 
 * 
 */
class Epicor_Common_Adminhtml_Epicorcommon_ImportexportController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_backupFolder;
    protected $_serializedData;
    protected $_serializedFinal;
    protected $_unserializedArray;
    protected $_serializedArray;
    protected $_module;
    protected $_table;
    protected $_key;
    protected $_id;
    protected $_mappingTables; 
    protected $_storesArray; 
    protected $_importFile; 
    protected $_excludedConfigTables = array('web/unsecure/base_url'
                                            ,'web/secure/base_url'
                                            ,'web/cookie/cookie_domain'
                                            ,'Epicor_Comm/licensing/type'
                                            ,'Epicor_Comm/licensing/erp'
                                            ,'Epicor_Comm/licensing/cert_file'
                                            ,'Epicor_Comm/licensing/username'
                                            ,'Epicor_Comm/licensing/password'
                                            ,'Epicor_Comm/licensing/company'
                                            ,'Epicor_Comm/licensing/ewa_username'
                                            ,'Epicor_Comm/licensing/ewa_password'
                                            ,'Epicor_Comm/licensing/p21_token_url'
                                        ); 

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function importAction() {
        
        if($this->getRequest()->getParam('importfile')){
            $this->_serializedArray = unserialize(base64_decode($this->getRequest()->getParam('importfile')));
//            $this->_serializedArray = base64_decode(unserialize($this->getRequest()->getParam('importfile')));
        }else{
             $this->_serializedArray = unserialize($this->_serializedArray);            
        }    
        foreach ($this->_serializedArray as $module => $value) {
            $this->_module = $module;
            foreach ($value as $key2 => $value2) {
                $this->_table = $key2;
                $this->_key = $value2['key'];
                $this->_id = $value2['id'];
                if($value2['data'] != 'No Data'){                       // don't load if no data
                    $this->_unserializedArray = unserialize($value2['data']);
                    if($this->_module."_".$this->_table == 'core_config_data'){
                        $this->importConfigData(); 
                    }else{
                        $this->importTableData();                           // all other tables
                    }
                }else{
                  Mage::getSingleton('core/session')->addNotice(Mage::helper('epicor_common')->__("{$this->_table} has no data to import"));
                }
            }
        }
        // if it gets here, all data has been imported successfully
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__("All data imported successfully"));
//        $this->_redirectReferer();  
        $this->_redirect('*/*/index');
    }
    public function importConfigData() {
        // 
        // retrieve list of stores on system  do not load data for store id's that aren't on system   
        $allStores = Mage::app()->getStores();
        foreach ($allStores as $eachStoreId => $val) {
            $storeId[] = Mage::app()->getStore($eachStoreId)->getId();
        }
        
        foreach ($this->_unserializedArray as $arrayEntry) {
    
            if(!$this->getRequest()->getParam('importall') && in_array($arrayEntry['path'], $this->_excludedConfigTables)){       // if includeall checkbox is clicked
                continue; 
            }
            
            if ($arrayEntry['scope_id'] == 0){                          // always import scope id 0  
                 $this->importDataCommonProcessing($arrayEntry);
            }else{                
                if(isset($this->_storesArray[$arrayEntry['scope_id']])){   // only proceed if current scope_id was selected and placed in storesarray
                    $arrayEntry['scope_id'] = $this->_storesArray[$arrayEntry['scope_id']];     // change scope_id to mapped scope id      
                    $this->importDataCommonProcessing($arrayEntry);
                }
            }


        }
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__("{$this->_module}_{$this->_table} data imported successfully"));
    }

    public function importTableData() {
        foreach ($this->_unserializedArray as $arrayEntry) {
            $this->importDataCommonProcessing($arrayEntry);
        }
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__("{$this->_module}_{$this->_table} data imported successfully"));
   
    }
    public function importDataCommonProcessing($arrayEntry) {

        // load model according to the value of the supplied key 
        $model = Mage::getModel("{$this->_module}" . "/" . "{$this->_table}")->getCollection();

        // build collection sql line by line using passed keys
        foreach ($this->_key as $key => $value) {                 // populate key with values to search on
            $model = $model->addFieldToFilter($key, array('eq' => $arrayEntry[$key]));      // build collection
        }
        $dataItem = $model->getFirstItem();                     // ensure only one item returned from collection

        unset($arrayEntry[$this->_id]);                         // remove old id field from saved data, so existing id is not overwritten
        if (!$dataItem->isObjectNew()) {                          // if object is not new, it is already on the table
            $arrayEntry[$this->_id] = $dataItem->getData($this->_id);   // save current id
            $dataItem->setData($arrayEntry);                            // apply all saved data to current id 
        }
        $dataItem = Mage::getModel("{$this->_module}" . "/" . "{$this->_table}")
                ->setData($arrayEntry);
        try {
            $dataItem->save();
        } catch (Exception $ex) {
            $this->errorMsg($ex);
        }
    }

    public function exportAction() {
       
        $mappingTablesArray = json_decode(base64_decode($this->getRequest()->getParam('mappingTablesArray')), true);
        $selectedTables = $this->getRequest()->getParam('mapping_row');
        $this->_mappingTables = array_intersect_key($mappingTablesArray,$selectedTables);   
        
        $this->_backupFolder = Mage::getBaseDir('var') . DS . 'backups' . DS . 'epicor_comm_settings' . DS . date('Y-m-d h-i-s') . DS;
        mkdir($this->_backupFolder, 0777, true);                  // backup folder will never previously exist
        if (!is_dir($this->_backupFolder)) {
            Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__(' Error: Unable to create backup directory'));
            $this->_redirectReferer();
        } else {
            
            $this->backupTables();

            $backupFile = $this->_backupFolder . "EpicorCommExport.txt";
            if (!file_put_contents($backupFile, $this->_serializedFinal)) {
                Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__(' Error: Backup file did not save successfully'));
                $this->_redirectReferer();
            } else {
                $this->_prepareDownloadResponse('epicor_comm_settings_' . date('Y-m-d h-i-s').'.txt', $this->_serializedFinal,'text/plain');
                //Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__('Backup created successfully'));
            }
        }
        
    }

    public function backupTables() {

        foreach ($this->_mappingTables as $key => $value) {
            $collection = Mage::getModel("{$value['module']}" . "/" . "{$value['entity']}")->getCollection();            
            if ($value['entity'] == 'config_data'){
                $excludeArray = array('web/unsecure/base_url', 'web/secure/base_url', 'web/cookie/cookie_domain'
                                       ,'Epicor_Comm/xmlMessaging/url','epicor_comm_enabled_messages/cim_request/ewa_url');
                $collection->addFieldToFilter('path',array('nin'=>$excludeArray));
            } 
            $collection = $collection->getData();
            
            if (empty($collection)) {         // if no data on table, don't serialized it
                $this->_serializedData[$value['module']][$value['entity']] = array('key' => $value['key'], 'data' => 'No Data', 'id' => $value['id']);
            } else {
                $this->_serializedData[$value['module']][$value['entity']] = array('key' => $value['key'], 'data' => serialize($collection), 'id' => $value['id']);
            }

            if ($this->_serializedData[$value['module']][$value['entity']]) {
             //   Mage::getSingleton('core/session')->addSuccess(Mage::helper('epicor_common')->__("{$value['module']}_{$value['entity']} backed up Successfully"));
            } else {
             //   Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__("Error: {$value['entity']} did not backup successfully"));
            }
        }
        $this->_serializedFinal = serialize($this->_serializedData);
    }
    public function errorMsg($ex){
        Mage::log($ex);
        Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__(" Error: {$this->_module}_{$this->_table} did not restore successfully"));
        Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER'])->sendResponse();
    }
    public function storemappingAction() {
        if(isset($_FILES['import_epicor_comm_settings_file']['tmp_name'])){
            if(!$_FILES['import_epicor_comm_settings_file']['tmp_name']){
                Mage::getSingleton('core/session')->addError(Mage::helper('epicor_common')->__("Please select an input file"));       
                Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER'])->sendResponse();   
            }else{       
                $this->_serializedArray = file_get_contents($_FILES['import_epicor_comm_settings_file']['tmp_name']);
                $unserializedArray = unserialize($this->_serializedArray);
                if(!isset($unserializedArray['core'])){
                    $this->importAction();     
                }else{
                    mage::register('importfile', $this->_serializedArray);
                    $this->loadLayout();
                    $this->renderLayout();
                }
            }    
        }          
//        $this->loadLayout();
//        $this->renderLayout();
        
        
    }
    public function setstoremappingAction() {
               
        $form_values  = $this->getRequest()->getParams();
        foreach($form_values as $key=>$value){
            if(strstr($key, 'store_selector_') !== false){
                $inputStoreNumber = explode('store_selector_', $key,2);
                if($value != 'not selected'){
                    $this->_storesArray[$inputStoreNumber[1]] = $value;
                }
            }
        }
        $this->importAction();
    }
}
