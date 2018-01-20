<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Flexi Layout');
    $this->addButton(100, array('label'=>'Import','onclick'=>"setLocation('".$this->getUrl('*/*/import')."');"), 1);
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Layout');
   
    parent::__construct();
  }
}