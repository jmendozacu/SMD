<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout_page';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Layout Pages');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Layout Page');
    parent::__construct();
  }
}