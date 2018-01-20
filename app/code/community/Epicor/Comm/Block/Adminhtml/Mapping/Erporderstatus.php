<?php
class Epicor_Comm_Block_Adminhtml_Mapping_Erporderstatus extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_mapping_erporderstatus';
    $this->_blockGroup = 'epicor_comm';
    $this->_headerText = Mage::helper('epicor_comm')->__('ERP Order Status Mapping');
    $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add Mapping');
    parent::__construct();
  }
}