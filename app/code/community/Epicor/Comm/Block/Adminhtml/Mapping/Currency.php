<?php
class Epicor_Comm_Block_Adminhtml_Mapping_Currency extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_currency';
    $this->_blockGroup = 'epicor_comm';
    $this->_headerText = Mage::helper('epicor_comm')->__('Currency Mapping');
    $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add Mapping');
    parent::__construct();
  }
}