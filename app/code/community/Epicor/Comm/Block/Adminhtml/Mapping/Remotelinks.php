<?php
class Epicor_Comm_Block_Adminhtml_Mapping_Remotelinks extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_remotelinks';
    $this->_blockGroup = 'epicor_comm';
    $this->_headerText = Mage::helper('epicor_comm')->__('Remote Links');
    $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add Remote Links');
    parent::__construct();
  }
}