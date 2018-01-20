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
class Epicor_Common_Adminhtml_Epicorcommon_NotesController extends Epicor_Comm_Controller_Adminhtml_Abstract {
    
     protected $_baseUrl = 'http://update.epicorcommerce.com/notes/';
     

    public function releaseAction() {
        $version = $this->getRequest()->getParam('version');
        $url = $this->_baseUrl. 'EpicorCommerceConnect_ReleaseNotes_'. $version . '.pdf';
        $this->getResponse()->setRedirect($url);
    }
    
    public function installAction() {
        $version = $this->getRequest()->getParam('version');
        $url = $this->_baseUrl. 'EpicorCommerceConnect_InstallGuide_'. $version . '.pdf';
        $this->getResponse()->setRedirect($url);
    }
	
    public function implementationAction() {
        $version = $this->getRequest()->getParam('version');
        $url = $this->_baseUrl. 'EpicorCommerceConnect_ImplementationGuide_'. $version . '.pdf';
        $this->getResponse()->setRedirect($url);
    }
	
    public function highlightsAction() {
        $version = $this->getRequest()->getParam('version');
        $url = $this->_baseUrl. 'EpicorCommerceConnect_VersionHighlights_'. $version . '.pdf';
        $this->getResponse()->setRedirect($url);
    }
	
    public function changelogAction() {
        $version = $this->getRequest()->getParam('version');
        $url = $this->_baseUrl. 'EpicorCommerceConnectChange_Log.pdf';
        $this->getResponse()->setRedirect($url);
    }
    
}
