<?php

class Epicor_Quotes_Block_Adminhtml_Quotes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_quotes';
    $this->_blockGroup = 'quotes';
    $this->_headerText = Mage::helper('quotes')->__('Quotes');
    parent::__construct();
    
    $this->removeButton('add');
  }
}