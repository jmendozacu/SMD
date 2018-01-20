<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Link
 *
 * @author Paul.Ketelle
 */
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Link extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_layout_block_navigation_link';
    $this->_blockGroup = 'flexitheme';
    $this->_headerText = Mage::helper('flexitheme')->__('Navigation Links');
    $this->_addButtonLabel = Mage::helper('flexitheme')->__('Add Navigation Link');
    parent::__construct();
  }
}


