<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Common_Block_Adminhtml_Access_Right extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
  {
    $this->_controller = 'adminhtml_access_right';
    $this->_blockGroup = 'epicor_common';
    $this->_headerText = Mage::helper('epicor_common')->__('Access Rights');
    $this->_addButtonLabel = Mage::helper('epicor_common')->__('Add New Right');
    parent::__construct();
  }
}

