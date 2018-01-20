<?php
class Epicor_Common_Block_Adminhtml_Importexport extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
         $this->setTemplate('epicor_common/comm_settings_backup/importExport.phtml');            
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Import / Export Comm Settings');
    }
}