<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
      
    $this->_controller = 'adminhtml_theme';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Flexi Skins');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Skin');
    
    $this->addButton(100, array('label'=>'Import','onclick'=>"setLocation('".$this->getUrl('*/*/import')."');"), 1);
    
    parent::__construct();
  }
}