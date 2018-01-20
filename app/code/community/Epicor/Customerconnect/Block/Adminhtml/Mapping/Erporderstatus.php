<?php
class Epicor_Customerconnect_Block_Adminhtml_Mapping_Erporderstatus extends Epicor_Common_Block_Adminhtml_Mapping_Default_Grid
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_erporderstatus';
    $this->_blockGroup = 'customerconnect';
    $this->_headerText = Mage::helper('customerconnect')->__('ERP Order Status Mapping');
    $this->_addButtonLabel = Mage::helper('customerconnect')->__('Add Mapping');
    parent::__construct();
  }
}