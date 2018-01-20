<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout_block';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Layout Blocks');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Layout Block');
    parent::__construct();
  }
}