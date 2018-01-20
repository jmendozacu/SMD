<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout_template';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Layout Templates');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Layout Template');
    parent::__construct();
  }
}