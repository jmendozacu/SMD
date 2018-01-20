<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   /**
     * Initialize Tabs
     *
     */
   // protected $_attributeTabBlock = 'epicor_comm/block_adminhtml_access_right_edit_tab_details';
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
         $this->setDestElementId('edit_form'); // this should be same as the form id define above
        $this->setTitle('Navigation Blocks');
    } 
 
  protected function _beforeToHtml()
  {
      
      $block=$this->getLayout()->createBlock('flexitheme/adminhtml_layout_block_navigation_edit_tab_details');

      $this->addTab('form_details', array(
          'label'     => 'General',
          'title'     => 'General Information',
          'content'   => $block->toHtml(),
      ));
     
      //$block_add_btn = $this->getLayout()->createBlock('flexitheme/adminhtml_layout_block_navigation_edit_tab_links_add');
      $block=$this->getLayout()->createBlock('flexitheme/adminhtml_layout_block_navigation_edit_tab_links');
      $this->addTab('form_links', array(
          'label'     => 'Links',
          'title'     => 'Navigation Links',
//          'content'   => $block_add_btn->toHtml().$block->toHtml(), 
          'content'   => $block->toHtml(),
      ));
      
      return parent::_beforeToHtml();
  }
}

