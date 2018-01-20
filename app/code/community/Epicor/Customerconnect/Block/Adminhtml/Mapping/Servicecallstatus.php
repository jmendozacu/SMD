<?php
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Servicecallstatus extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_servicecallstatus';
    $this->_blockGroup = 'customerconnect';
    $this->_headerText = Mage::helper('customerconnect')->__('Service Call Status Mapping');
    $this->_addButtonLabel = Mage::helper('customerconnect')->__('Add Mapping');
    parent::__construct();
  }
}