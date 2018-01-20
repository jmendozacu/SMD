<?php
class Epicor_FlexiTheme_Adminhtml_Flexitheme_TranslationController extends Epicor_Comm_Controller_Adminhtml_Abstract {

    protected $_message = Array();
    protected $_ajaxMessage = Array();
    protected $_newPhraseList = Array();
    protected $_oldPhraseList = Array();
    protected $_rowAdded;
    protected $_rowRemoved;
    protected $_toBeAdded;
    protected $_toBeDeleted;
    protected $_translatedArray;
    protected $_autoTranslate;
    protected $_language;
    protected $_count = 1;
    public function _construct(){
        $this->_translatePath =  Mage::getBaseDir('app').DS."locale".DS;
    }
    public function indexAction() {
     #   $this->callToGoogle();
        Mage::app()->cleanCache('TRANSLATE');               // clear translation cache 
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_redirect('*/*/edit');
    }

    public function editAction() {
        $this->loadLayout();
        if (Mage::app()->getRequest()->getParam('id')) {
            $language = Mage::getModel('flexitheme/translation_language')->load(Mage::app()->getRequest()->getParam('id'));
            Mage::register('translation_language_data', $language);
        }

        $this->renderLayout();
    }

    public function deleteLanguageAction() {
        $id = $this->getRequest()->getParam('id');
        $languageObject = Mage::getModel('flexitheme/translation_language')->load($id);
        
        if (!$languageObject->isObjectNew()) {
            $model = Mage::getModel('flexitheme/translation_language')->load($id);            
            try {
                $languageObject->delete();
                $this->_message = $this->__("{$languageObject->getTranslationLanguage()} ({$languageObject->getLanguageCode()}) has been successfully removed from the table.");
                Mage::getSingleton('core/session')->addSuccess($this->_message);
            } catch (Exception $e) {
                Mage::log('Update of epicor_flexitheme_translation_language failure: ' . $e->getMessage());
                $this->_message = $this->__("Delete of '{$languageObject->getTranslationLanguage()}' from the Translation Language Table has failed. See log.");
                Mage::getSingleton('core/session')->addError($this->_message);
            }
        }
        session_write_close();
        $this->_redirect('*/*/index');  
    }

    public function saveLanguage() {
        
        $languageCode = $this->getRequest()->getPost('languageCode');
        $translationLanguage = $this->getRequest()->getPost('language');
        $currentLanguageData = Mage::getModel('flexitheme/translation_language')->load($languageCode, 'language_code');
        $active = ucfirst($this->getRequest()->getPost('active'));
        $doesEntryExist = $currentLanguageData->getData();
        $originalLanguageCode = Mage::getModel('flexitheme/translation_language')->load($this->getRequest()->getParam('id'))->getLanguageCode(); 
        if (!$currentLanguageData->isObjectNew()) {
            if ($currentLanguageData->getTranslationLanguage() == $translationLanguage 
                    && $currentLanguageData->getLanguageCode() == $originalLanguageCode 
                    && $currentLanguageData->getActive() == $active) {

//                $this->_message = $this->__('No Changes Requested to Translation Language Table');
                $this->_message = $this->__("No Changes Requested to {$translationLanguage} ({$originalLanguageCode}) on Translation Language Table");
                Mage::getSingleton('core/session')->addNotice($this->_message);
            } else {
                try {
                    if($originalLanguageCode != $languageCode){
                            $this->_message = $this->__("Translation for language code: {$languageCode} already exists. Language name: {$currentLanguageData->getTranslationLanguage()}. Unable to continue");
                            Mage::getSingleton('core/session')->addNotice($this->_message);
                    }else{
                        $currentLanguageData->setTranslationLanguage($translationLanguage)
//                                            ->setLanguageCode($languageCode)
                                            ->setActive($active)
                                            ->save();
                        $this->_message = $this->__("{$translationLanguage} ({$languageCode}) has been Updated on the Translation Language Table");
                        Mage::getSingleton('core/session')->addSuccess($this->_message);
                    }
                } catch (Exception $e) {
                    Mage::log('update of epicor_flexitheme_translation_language failure: ' . $e->getMessage());
                    $this->_message = $this->__("Update of Translation Language Table has failed. See log");
                    Mage::getSingleton('core/session')->addError($this->_message);
                }
            }
        } else {
            // CREATE Entry
            try {
//                $newLanguageData = Mage::getModel('flexitheme/translation_language')->load($languageCode, 'language_code'); 
//                if(!$newLanguageData->isObjectNew()){                                                             // language already exists, so ignore
//                    $newLanguageId = $newLanguageData->getId();
//                    $this->_message = $this->__("Translation for language code: {$newLanguage} already exists. Language name: {$newLanguageData->getTranslationLanguage()}. Unable to continue");
//                    Mage::getSingleton('core/session')->addNotice($this->_message);
//                }else{
                    
                    $currentLanguageData->setTranslationLanguage($translationLanguage)
                                            ->setLanguageCode($languageCode)
                                            ->setActive($active)
                                            ->save();
                    
                    $this->_message = $this->__("{$translationLanguage} ({$languageCode}) has been added to the Translation Language Table");
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                    
                    if($originalLanguageCode){                          // if not completely new language copy old stuff
                        $this->copyTranslationsToNewLanguage($originalLanguageCode, $this->getRequest()->getParam('id'),$languageCode);
                    }
                
            } catch (Exception $e) {
                Mage::log('Insert of entry to epicor_flexitheme_translation_language failure: ' . $e->getMessage());
                $this->_message = $this->__('Update of Translation Language Table has failed. See log.');
                Mage::getSingleton('core/session')->addError($this->_message);
            }
        }
    }
    
    public function copyTranslationsToNewLanguage($oldLanguage, $oldLanguageId, $newLanguage){
        $newLanguageData = Mage::getModel('flexitheme/translation_language')->load($newLanguage,'language_code');
        $oldLanguageData = Mage::getModel('flexitheme/translation_language')->load($oldLanguage,'language_code');
        $newLanguageId = $newLanguageData->getId();
        $newLanguageName = $newLanguageData->getTranslationLanguage();
        $oldLanguageName = $oldLanguageData->getTranslationLanguage();
        
        $updatesCollection = Mage::getModel('flexitheme/translation_updates')->getCollection();
        $updatesCollection->getSelect()->joinLeft(array('td' => 'epicor_flexitheme_translation_data'), 'main_table.phrase_id = td.id', array('td.translation_string'));
        $updatesCollection->addFieldToFilter('language_id', array('eq' => $oldLanguageId));
        $updates = $updatesCollection->getData();
           
        // copy db translation entries to new language
        foreach($updatesCollection as $update){
            try{
                $update->setId(null);
                $update->setLanguageId($newLanguageId);
                $update->save();
            } catch (Exception $ex) {
                $this->_message = $this->__("Failed to copy existing translations in {$oldLanguageName} ({$oldLanguage}) on Database to {$oldLanguageName} ({$newLanguage}). See Log.");
                Mage::getSingleton('core/session')->addError($this->_message);
                Mage::log('--- Error copying to database in flexitheme translate---');
                Mage::log($ex);
                return;
            }
        }
        $oldLocalePath = $this->_translatePath . $oldLanguage;
        $newLocalePath = $this->_translatePath . $newLanguage;
        if (file_exists($newLocalePath.DS."translate.csv")) {   // delete new file translate.csv if exists
            $file = $newLocalePath.DS."translate.csv";
            unset($file);
        }            
        if (file_exists($oldLocalePath.DS."translate.csv")){
            mkdir($newLocalePath, 0777, true);
            copy($oldLocalePath.DS."translate.csv", $newLocalePath.DS."translate.csv");
             $this->_message = $this->__("Existing translations in {$oldLanguageName} ({$oldLanguage}) copied to {$newLanguageName} ({$newLanguage}). ");
             Mage::getSingleton('core/session')->addSuccess($this->_message);
        }else{
            $this->_message = $this->__("No translations to copy from {$oldLanguage} to {$newLanguage}");
            Mage::getSingleton('core/session')->addNotice($this->_message);
        }    
    }
    public function nextPagePhrasesAction() {

        $language = Mage::getModel('flexitheme/translation_language')->load($this->getRequest()->getParam('id'));

        if (!Mage::registry('translation_language_data')) {
            Mage::register('translation_language_data', $language);
        }
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('flexitheme/adminhtml_translation_edit_tabs_phrases', 'content')->toHtml()
        );
    }

    public function saveTranslationPhrase($phraseId, $translatedPhrase) {
        
        $languageCode = $this->getRequest()->getPost('languageCode');
        $languageDetails = Mage::getModel('flexitheme/translation_language')->load($languageCode, 'language_code');
        $language = $languageDetails->getTranslationLanguage();
     
        if (!$languageDetails->isObjectNew()) {
            $phraseCheck = Mage::getModel('flexitheme/translation_data')->load($phraseId);
            if ($phraseCheck->isObjectNew()) {
                $this->_message = Array('message' => 'Phrase does not exist on the table.', 'type' => 'error');
                Mage::getSingleton('core/session')->addError($this->_message);
                return;
            }
            // if phrase exists, check if a translation exists for it for this language on the translation_update table              
            $updatesCollection = Mage::getModel('flexitheme/translation_updates')->getCollection()
                    ->addFieldToFilter('phrase_id', array('eq' => $phraseId))
                    ->addFieldToFilter('language_id', array('eq' => $languageDetails->getId()))
                    ->getFirstItem();
            // retrieve original string 
            $origPhrase = Mage::getModel('flexitheme/translation_data')->load($phraseId);

            if (!$updatesCollection->isObjectNew()) {
                if ($updatesCollection->getTranslatedPhrase() == $translatedPhrase) {         // if no changes made
                    $this->_message = $this->__('Translation Phrases Table Not Updated.');
                    Mage::getSingleton('core/session')->addNotice($this->_message);
                    
                } else {
                    if ($translatedPhrase) {                    // if translated phrase has been cleared, delete entry, don't just update it      
                        try {
                            $updatesCollection->setTranslatedPhrase($translatedPhrase)->save();
                            $this->_message = $this->__($language . ' Translation for phrase: "' . $origPhrase->getTranslationString() . '" has been updated successfully to "' . $translatedPhrase . '"');
                            Mage::getSingleton('core/session')->addSuccess($this->_message);
                        } catch (Exception $e) {
                            Mage::log("Error updating phrase: \"{$phraseCheck->getTranslationString()} on epicor_flexitheme_translation_updates table: " . $e->getMessage());
                            $this->_message = $this->__("Error updating phrase: \"{$phraseCheck->getTranslationString()}\" on Translation Updates table ");
                            Mage::getSingleton('core/session')->addError($this->_message);
                        }
                    } else {
                        try {
                            $updatesCollection->delete();
                            $this->_message = $this->__($language . ' translation for phrase "' . $origPhrase->getTranslationString() . '" has been deleted from the Translation Updates table successfully');
                            Mage::getSingleton('core/session')->addSuccess($this->_message);
                        } catch (Exception $e) {
                            Mage::log("Error deleting translation phrase for : \"{$origPhrase->getTranslationString()} on epicor_flexitheme_translation_updates table: " . $e->getMessage());
                            $this->_message = $this->__("Error deleting {$language} translation phrase for:  \"{$origPhrase->getTranslationString()}\" on Translation Updates table: ");
                            Mage::getSingleton('core/session')->addError($this->_message);
                        }
                    }
                }
            } else {
                // CREATE Entry                  
                $updatesCollection->setPhraseId($phraseId);
                $updatesCollection->setTranslatedPhrase($translatedPhrase);  
                $updatesCollection->setLanguageId($languageDetails->getId());  

                try {
                    $updatesCollection->save();
                    $this->_message = $this->__("\"{$translatedPhrase}\", " . $language . " translation for phrase \"{$origPhrase->getTranslationString()}\" was successfully added to the Phrase Translation table");
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                } catch (Exception $e) {
                    Mage::log("Insert of \"{$translatedPhrase}\" to epicor_flexitheme_translation_updates failure: " . $e->getMessage());
                    $this->_message = $this->__("Addition of translation for {$language} phrase \"{$origPhrase->getTranslationString()}\" has failed. See log.");
                    Mage::getSingleton('core/session')->addError($this->_message);
                }
            }
        }
    }

    public function saveAction() {
        if($this->getRequest()->getPost('languageCode')){                                       // only process if language specified            
            $this->saveLanguage();
            if ($this->getRequest()->getParam('translated_phrases')) {
                $translatedPhrases = json_decode($this->getRequest()->getParam('translated_phrases'), true);
            
                foreach ($translatedPhrases as $key => $value) {
                    $keyId = explode("translatedPhrase_", $key);
                    $this->saveTranslationPhrase($keyId[1], $value);
                }
                $this->updateTranslationCsvFile();
            }
           
            echo json_encode($this->_ajaxMessage = Array('redirect' => "{$this->getUrl('*/*/index')}", 'type' => 'success'));
        } else{                                                                              // if no language specified
             $this->_message = $this->__("Unable to save as no Language specified");
             Mage::getSingleton('core/session')->addError($this->_message);
             echo json_encode($this->_ajaxMessage = Array('redirect' => "{$this->getUrl('*/*/edit')}", 'type' => 'success'));
        }    
    }
    
    public function updateTranslationCsvFile($languageId = false, $languageCode = false) {      // languageId and code will only be populated from singleTranslate 
        $updatesCollection = Mage::getModel('flexitheme/translation_updates')->getCollection();
        $updatesCollection->getSelect()->joinLeft(array('td' => 'epicor_flexitheme_translation_data'), 'main_table.phrase_id = td.id', array('td.translation_string'));
        $languageCode = ($languageCode)? $languageCode : $this->getRequest()->getParam('languageCode');
        $languageId = ($languageId)? $languageId : $this->getRequest()->getParam('id'); 
        $updatesCollection->addFieldToFilter('language_id', array('eq' => $languageId));
        $updates = $updatesCollection->getData();
        $localePath = $this->_translatePath . $languageCode;
        if (!file_exists($localePath)) {    // check if locale directory exists and create it if not
            mkdir($localePath, 0777, true);
        }
        $file = $localePath .DS."translate.csv";
        $tempFile =  $localePath .DS. "translate" . date('YmdHis') . ".csv";
        if (file_exists($file)) {
            rename($file, $tempFile);
        }
        $fileContents = "";
        
        foreach ($updates as $update) {
            $fileContents .= "\"" . str_replace('"', '""',$update['translation_string']) . "\",\"" . str_replace('"', '""', $update['translated_phrase']) . "\"\n";
        }
        if(file_put_contents($file, $fileContents)){                    // write new translation file 
            if(file_exists($tempFile)){
                 unlink($tempFile);
            }        
        };  
    }

    public function updatePhrasesAction() {

        $it = new RecursiveDirectoryIterator(Mage::getBaseDir('app'));     

        $dataCollection = Mage::getModel('flexitheme/translation_data')->getCollection()   // get current phrase list as array 
                            ->addfieldToFilter('is_custom', array('eq'=>0));
        $dataCollection->addFieldToSelect('translation_string');
        $collection = $dataCollection->getData();
        
        foreach ($collection as $key => $value) {                           // get value only and add to new array
            foreach ($value as $key2 => $value2) {
                $this->_oldPhraseList[] = $value2;
            }
        }
        // process file   
        foreach (new RecursiveIteratorIterator($it) as $list => $file) {
            if (!strstr($list, '\\Adminhtml\\')) {                               // ignore Adminhtml folders 
                $this->processEachFile($list);                                   // file name is stored as key, so pass key
            }
        }
//        $x = array_pop($this->_newPhraseList); //for testing
        $this->_toBeDeleted = array_diff($this->_oldPhraseList, $this->_newPhraseList);                   // returns all entries on oldPhraseList not present on newPhraseList
        $this->_toBeAdded = array_diff($this->_newPhraseList, $this->_oldPhraseList);                     // returns all entries on new list not on original list to be added
        
        $this->rowsToBeAdded();
        $this->rowsToBeDeleted();

        if (!$this->_rowAdded && !$this->_rowRemoved) {                             // if no changes needed
            $this->_message = $this->__('Scan Complete. No Changes to Translation Tables Required');
            Mage::getSingleton('core/session')->addNotice($this->_message);
        }

        session_write_close();
        echo json_encode($this->_ajaxMessage = Array('redirect' => "{$this->getUrl('*/*/index')}", 'type' => 'success'));
    }
    function rowsToBeAdded(){
        $firstLoad = Mage::getModel('flexitheme/translation_data')->getCollection()   // check if table has data already
                         ->addfieldToFilter('is_custom', array('eq'=>0));
        $collectionSize = $firstLoad->getSize();        // has data been written already
            foreach ($this->_toBeAdded as $newRow) {           
           
            $newPhrase = Mage::getModel('flexitheme/translation_data')
                    ->setTranslationString($newRow);
            try {
                $newPhrase->save();               
                if($collectionSize){                        // don't display message if loading everything
                    $this->_message = $this->__('Phrase "' . $newRow . '" added to Translation Data table successfully');
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                }   
              
                $this->_rowAdded = 1;
            } catch (Exception $e) {
                Mage::log('Insert of data to epicor_flexitheme_translation_data failed: ' . $e->getMessage());
                $this->_message = $this->__('Update of Translation Data Table has failed. See log.');
                Mage::getSingleton('core/session')->addError($this->_message);
            }
        }
        if(!$collectionSize){                    
            $this->_message = $this->__('Initial population of translation_data table successful');
            Mage::getSingleton('core/session')->addSuccess($this->_message);
        }    
    }
    function rowsToBeDeleted(){
        $delete = Mage::getSingleton('core/resource')->getConnection('core_write');
        $translationDataModel = Mage::getModel('flexitheme/translation_data');
        foreach ($this->_toBeDeleted as $key => $oldRow) {
            try {
                $datarow = $translationDataModel->load($oldRow,'translation_string');
                if(!$datarow->getIsCustom()){
                    $translationDataModel->setId($datarow->getId())->delete();
                    $this->_message = $this->__('Phrase "'.$oldRow.'" deleted from Translation_Data table successfully',$oldRow);
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                    $this->_rowRemoved = 1;
                }    
            } catch (Exception $e) {
                Mage::log('Delete of entry from epicor_flexitheme_translation_data failed: ' . $e->getMessage());
                $this->_message = $this->__('Delete of "' . $oldRow . '" from Translation_Data Table has failed. See log.');
                Mage::getSingleton('core/session')->addError($this->_message);
            }
        }
    }
    function processEachFile($fileName) {
        $trimmed = file($fileName, FILE_SKIP_EMPTY_LINES);                           // reads content of file
        foreach ($trimmed as $line_num => $line) {

            $regPattern = '/__\(["\'](.*?)["\'][\),]/';

            preg_match_all($regPattern, $line, $matches);

            if ($matches) {                    // if there are any matches                            
                foreach ($matches[1] as $key => $value) {
                    if (!in_array($value, $this->_newPhraseList) && $value) { // if not already in the array and value not empty add it 
                        $this->_newPhraseList[] = $value;
                    }
                }
            }
        }
    }
    function singleTranslateAction($auto = false) {                                 // $auto will only be populated from autoTranslate method
        if($this->_autoTranslate){                                          // comes from autotranslate method
           $languageId = $this->getRequest()->getParam('id');
           $languageCode = $this->getRequest()->getParam('languageCode');
//           $language = $this->getRequest()->getParam('language');
           $translationStringId = $auto['id'];
           $originalPhrase = $auto['translation_string'];
        }else{                                              // single translate
            $rowData = array();
            $helper = Mage::helper('flexitheme');
            $rowData = json_decode($helper->urlDecode($this->getRequest()->getParam('rowdata')), true);
            $languageId = $rowData['language_id'];
            $languageArray = Mage::getModel('flexitheme/translation_language')->load($languageId);
            $languageCode = $languageArray->getLanguageCode();
            $this->_language = $languageArray->getTranslationLanguage();
            $translationStringId = $rowData['id'];
            $originalPhrase = $rowData['translation_string'];
        }    
        $toLanguage = substr($languageCode, 0, strpos($languageCode, '_'));  // extract required part of language code eg 'en' from 'en_GB' 
                
        // Retrieve translation from google 
        $phrase = rawurlencode($originalPhrase);
        $url = "http://translate.google.com/translate_a/t?client=1&sl=en&tl=" . $toLanguage . "&q=" . $phrase;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = utf8_encode(curl_exec($ch));
        curl_close($ch);
        $returnedData = json_decode($response, true);
        if(is_array($returnedData)){
            $translatedPhrase = $returnedData['sentences'][0]['trans'];
        }else{
            $translatedPhrase = $returnedData;
        }
        
        // check if update for phrase is on database
        $existingEntry = Mage::getModel('flexitheme/translation_updates')->getCollection()
                ->addFieldToFilter('phrase_id', array('eq' => $translationStringId))
                ->addFieldToFilter('language_id', array('eq' => $languageId))
                ->getFirstItem();
        
        // write translation to database table only if data has been changed
        if (!$existingEntry->isObjectNew()) {
            if ($translatedPhrase != $existingEntry->getTranslatedPhrase()) {
                $existingEntry->setTranslatedPhrase($translatedPhrase);
                try {   
                    $existingEntry->save();
                    if(!$this->_autoTranslate){                 // don't write message if part of auto translate
                        $this->_message = $this->__("\"{$translatedPhrase}\", " . $this->_language . " translation for phrase \"{$originalPhrase}\", was successfully added to the Phrase Translation table");
                        Mage::getSingleton('core/session')->addSuccess($this->_message);
                    }
                } catch (Exception $e) {
                    Mage::log("Update of \"{$translatedPhrase}\" to epicor_flexitheme_translation_updates failure: " . $e->getMessage());
                    $this->_message = $this->__('Update of translation for "' . $this->_language . ' phrase "' . $originalPhrase . '" has failed. See log.');
                    Mage::getSingleton('core/session')->addError($this->_message);
                }
            } else {
                if(!$this->_autoTranslate){ 
                    $this->_message = $this->__("\"{$translatedPhrase}\", " . $this->_language . " translation for phrase \"{$originalPhrase}\", already exists.");
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                }    
            }
        } else {                   
            $this->createTranslationRecord($translationStringId, $languageId, $translatedPhrase);
        }
        if(!$this->_autoTranslate){                         // if function not fired from autotranslate, redirect
            $this->updateTranslationCsvFile($languageId, $languageCode);
            $this->_redirect('*/*/index');
        }    
    }
    function autoTranslateAction(){ 
        // seems pointless translating into another version of English, if English is locale language.
        // But what if Spanish is, or French? Look into!
//        $locale = Mage::getStoreConfig('general/locale/code');
//        if($locale == $this->getRequest()->getParam('languageCode')){
//            $this->_message = "Cannot translate into same language as locale language. No action taken";                    
//            Mage::getSingleton('core/session')->addNotice($this->_message);
//            echo json_encode($this->_ajaxMessage = Array('redirect' => "{$this->getUrl('*/*/index')}", 'type' => 'success'));
//        }       
        $this->_autoTranslate = true;
        $this->_language = $this->getRequest()->getParam('language');
        $dataCollection = Mage::getModel('flexitheme/translation_data')->getCollection()
                           ->addFieldToFilter('is_custom', array('eq' => 0));
        $originalPhrases = $dataCollection->getData();                      // contains all translation phrases.           
        foreach($originalPhrases as $key=>$value){
//             $this->_count++;
//             Mage::log( $this->_count);
            $this->singleTranslateAction($value);          
        }
        $this->updateTranslationCsvFile();
        $this->_message = $this->__("Auto Translate into {$this->_language} complete.");
        Mage::getSingleton('core/session')->addSuccess($this->_message);
        echo json_encode($this->_ajaxMessage = Array('redirect' => "{$this->getUrl('*/*/index')}", 'type' => 'success'));
    }

    function createTranslationRecord($phraseId, $languageId, $translatedPhrase){
            // CREATE Entry  
            $language = Mage::getmodel('flexitheme/translation_language')->load($languageId)->getTranslationLanguage();
            $newEntry = Mage::getModel('flexitheme/translation_updates');
            $newEntry->setPhraseId($phraseId);
            $newEntry->setLanguageId($languageId);
            $newEntry->setTranslatedPhrase($translatedPhrase);    
            $translationPhrase = Mage::getModel('flexitheme/translation_data')->load($phraseId);    // get original string value for message  
            $origPhrase = new Varien_Object($translationPhrase->getData());

            try {
                $newEntry->save();
                if(!$this->_autoTranslate){
                    $this->_message = $this->__("\"{$translatedPhrase}\", " . $language . " translation for phrase \"{$origPhrase->getTranslationString()}\", was successfully added to the Phrase Translation table");
                    Mage::getSingleton('core/session')->addSuccess($this->_message);
                }    
            } catch (Exception $e) {
                Mage::log("Insert of \"{$translatedPhrase}\" to epicor_flexitheme_translation_updates failure: " . $e->getMessage());

                $this->_message = $this->__('Addition of translation for "' . $language . ' phrase "' . $origPhrase->getTranslationString() . '" has failed. See log.');
                Mage::getSingleton('core/session')->addError($this->_message);
            }
    }
    private function joinFiles(array $files, $result)
    {
        if(!is_array($files)) {
            throw new Exception('Input to joinfiles must be an array');
        }

        $resultFile = fopen($result, "w+");

        foreach($files as $file) {
            $inputFile = fopen($file, "r");
            while(!feof($inputFile)) {
                fwrite($resultFile, fgets($inputFile));
            }
            fclose($inputFile);
            unset($inputFile);
            fwrite($resultFile, "\n"); //usually last line doesn't have a newline
        }
        fclose($resultFile);
        unset($resultFile);
    }
}
