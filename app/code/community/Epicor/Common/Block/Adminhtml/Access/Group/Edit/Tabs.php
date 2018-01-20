<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Epicor_Common_Block_Adminhtml_Access_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
      /**
     * Initialize Tabs
     *
     */
   // protected $_attributeTabBlock = 'epicor_common/block_adminhtml_access_right_edit_tab_details';
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
         $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Access Group');
    } 
 
  protected function _beforeToHtml()
  {
      $block=$this->getLayout()->createBlock('epicor_common/adminhtml_access_group_edit_tab_details');

      $this->addTab('form_details', array(
          'label'     => 'General',
          'title'     => 'General Information',
          'content'   => $block->toHtml(),
      ));
     
      $group = Mage::registry('access_group_data');
      $this->addTab('form_rights', array(
          'label'     => 'Access Rights',
          'title'     => 'Access Rights in this group',
          'url'   => $this->getUrl('*/*/rights',array('id'=>$group->getId(),'_current' => true)),
          'class'   =>'ajax'
      ));
      
      $this->addTab('form_customers', array(
          'label'     => 'Customers',
          'title'     => 'Customers in this group',
          'url'       => $this->getUrl('*/*/customer', array('id'=>$group->getId(),'_current' => true)),
          'class'     => 'ajax',
      ));
      
      return parent::_beforeToHtml();
  } 
}

