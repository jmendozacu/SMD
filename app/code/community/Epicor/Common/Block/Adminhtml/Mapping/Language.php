<?php
class Epicor_Common_Block_Adminhtml_Mapping_Language extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_language';
    $this->_blockGroup = 'epicor_common';
    $this->_headerText = Mage::helper('epicor_common')->__('Language Mapping');
    $this->_addButtonLabel = Mage::helper('epicor_common')->__('Add Mapping');
    parent::__construct();
  }
}