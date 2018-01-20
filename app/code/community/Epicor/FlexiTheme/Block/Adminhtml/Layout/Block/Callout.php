<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Callout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout_block_callout';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Callout Blocks');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Callout Block');
    parent::__construct();
  }
}