<?php
class Epicor_Common_Block_Adminhtml_Importexport_Storemapping extends Mage_Adminhtml_Block_Widget
{
    protected $_storesList;
    protected $_storeNumber = 0;
    protected $_inputfile;
    public function __construct()
    {
        $this->_serializedArray = unserialize(file_get_contents($_FILES['import_epicor_comm_settings_file']['tmp_name']));
         // extract the stores from the config data and place into $this->_storeslist array
          foreach ($this->_serializedArray as $key=>$arrayEntry) {
              if(isset($arrayEntry['config_data']['data'])){
                    $configData = $arrayEntry['config_data']['data'];
                    foreach (unserialize($arrayEntry['config_data']['data']) as $key=>$value){
                        $this->_storesList[$value['scope_id']] = $value['scope_id'];
                    }
                    array_shift($this->_storesList);     // remove 0 from beginning of array as we don't want it to be displayed
              } 
          }
              
    }
 
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Import / Export Store Mapping Settings');
    }
}