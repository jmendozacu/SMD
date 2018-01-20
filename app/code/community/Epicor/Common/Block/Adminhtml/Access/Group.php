<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Epicor_Common_Block_Adminhtml_Access_Group extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
  {
    $this->_controller = 'adminhtml_access_group';
    $this->_blockGroup = 'epicor_common';
    $this->_headerText = Mage::helper('epicor_common')->__('Access Groups');
    $this->_addButtonLabel = Mage::helper('epicor_common')->__('Add New Group');
    parent::__construct();
  }
}

