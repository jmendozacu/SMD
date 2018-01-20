<?php
//include_once("Epicor\FlexiTheme\controllers\Adminhtml\TranslationController.php");
//class Epicor_FlexiTheme_Adminhtml_UsertranslationController extends Epicor_FlexiTheme_Adminhtml_Flexitheme_TranslationController 
class Epicor_FlexiTheme_Adminhtml_Flexitheme_UsertranslationController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    public function _construct(){
        $this->_translatePath =  Mage::getBaseDir('app').DS."locale".DS;
    }
    public function indexAction() {
        Mage::app()->cleanCache('TRANSLATE');               // clear translation cache 
        $this->loadLayout();
        $this->renderLayout();
    }
    public function addBaseUserTranslationAction() {
        // when updating phrase list, ignore user translations (is_custom = true)
        
        // check if translation exists on table already
        $userTranslationString = trim($this->getRequest()->getParam('user_translation_string'));
        $originalString = Mage::getModel('flexitheme/translation_data')->getCollection()         // collection added due to quirk that recognises different case as the same
                            ->addFieldToFilter('translation_string', array('eq'=>$userTranslationString, 'translation_string'));
        if($originalString->getFirstItem()->isObjectNew()){                                 // if doesn't already exist, create it
            $this->addCustomTranslation($userTranslationString);
        }else{                                                                               // if more than one match, check if any translations are case identical 
            $alreadyExists = false;
            foreach($originalString as $string){
                if($userTranslationString == trim($string->getTranslationString())){
                     $alreadyExists = true;
                     break;
                }
            }             
           if(!$alreadyExists){  // if it doesn't already exist add to table 
                $this->addCustomTranslation($userTranslationString);
           }else{     
                if(!$string->getIsCustom()){
                    Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('flexitheme')->__("'User translation string: '{$userTranslationString}' already exists on the table
                                                         as a standard string. Cannot add."), true);
                }else{
                    try{
                        Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('flexitheme')->__("'User translation string: '{$userTranslationString}' already added to table. No Action Required"), true);
                    }catch(exception $e){
                        $this->userTranslationErrorMessage('updating', $e, $userTranslationString);
                    }
                }
             }    
        }   
        $this->_redirect('*/*/index');
    }
    
    public function userTranslationConfirmMessage($action , $userTranslationString) {
        Mage::getSingleton('core/session')->addSuccess(
                    Mage::helper('flexitheme')->__("'User translation string: '{$userTranslationString} '
                                                     {$action} translation table successfully"), true);
    }
    public function userTranslationErrorMessage($action, $e, $userTranslationString) {
        Mage::getSingleton('core/session')->addError(
                    Mage::helper('flexitheme')->__("Error {$action} '{user translation string: '{$userTranslationString}'. Please try again later"), true);
        Mage::log('--- flexitheme translation error translation data table ---'); 
        Mage::log($e);
    }
    public function deleteBaseUserTranslationEntryAction() {
        $id = $this->getRequest()->getParam('id');
        $translationDataModel = Mage::getModel('flexitheme/translation_data');
        //delete translated phrase from translation_updates
        $userTranslationString = $translationDataModel->load($id);
        $translationUpdates = Mage::getModel('flexitheme/translation_updates')->getCollection()
                                ->addFieldToFilter('phrase_id', array('eq' => $id));
        $updateData = $translationUpdates->getFirstItem();
        if(!$updateData->isObjectNew()){                // if object is not new continue (only new if no translations for custom string)
            foreach($translationUpdates as $updateString){
                try{
                    $updateString->delete();          //delete from translation_updates
                    $this->updateTranslationCsvFile($updateString->getLanguageId());
                } catch (Exception $e) {
                    $this->userTranslationErrorMessage('deleting',$e, $userTranslationString->getTranslationString());
                    Mage::log('--- error deleting translation for base user translation entry---');
                    Mage::log($e);
                }
            }    
            try{
                $userTranslationString->delete();             //delete entry from translation_data
                $this->userTranslationConfirmMessage('deleted from', $userTranslationString->getTranslationString());    
            } catch (Exception $ex) {
                $this->userTranslationErrorMessage('deleting',$e, $userTranslationString->getTranslationString());
                Mage::log('--- error deleting base user translation entry---');
                Mage::log($e);
            }
        }else{            //if no data on translation updates, delete entry from translation_data only 
            try{
                $userTranslationString->delete();
                $this->userTranslationConfirmMessage('deleted from', $userTranslationString->getTranslationString());    
                
            } catch (Exception $ex) {
                $this->userTranslationErrorMessage('deleting', $e, $userTranslationString->getTranslationString());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function updateTranslationCsvFile($languageId) {
        $updatesCollection = Mage::getModel('flexitheme/translation_updates')->getCollection();
        $updatesCollection->getSelect()->joinLeft(array('td' => 'epicor_flexitheme_translation_data'), 'main_table.phrase_id = td.id', array('td.translation_string'));
        $updatesCollection->addFieldToFilter('language_id', array('eq' => $languageId));
        $updates = $updatesCollection->getData();
        $languageCode = Mage::getSingleton('flexitheme/translation_language')->load($languageId)->getLanguageCode();
        $path = Mage::getBaseDir('app');     // get base url 
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
            $fileContents .= "\"" . $update['translation_string'] . "\",\"" . $update['translated_phrase'] . "\"\n";
        }
        if(file_put_contents($file, $fileContents) ||$fileContents == 0){                    // write new translation file 
            if(file_exists($tempFile)){
                 unlink($tempFile);
            }        
        };  
    } 
    public function nextPageUsertranslationsAction() {

        $language = Mage::getModel('flexitheme/translation_language')->load($this->getRequest()->getParam('id'));

        if (!Mage::registry('translation_language_data')) {
            Mage::register('translation_language_data', $language);
        }
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('flexitheme/adminhtml_usertranslation_edit_tabs_usertranslations', 'content')->toHtml()
        );
    }
    private function  addCustomTranslation($userTranslationString){ 
        $originalString = Mage::getModel('flexitheme/translation_data');
        try{
           $originalString->setTranslationString($userTranslationString);
           $originalString->setIsCustom(true);
           $originalString->save();
           $this->userTranslationConfirmMessage('added to', $userTranslationString);    

        } catch (Exception $e) {
            $this->userTranslationErrorMessage('adding', $e, $userTranslationString);
        }
    }        

}
