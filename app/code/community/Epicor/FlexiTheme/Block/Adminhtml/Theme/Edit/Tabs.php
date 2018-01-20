<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setTitle('Theme Design');
    } 
 
  protected function _prepareLayout()
  {
      $id = Mage::registry('theme_id');

      $block=$this->getLayout()->createBlock('flexitheme/adminhtml_theme_edit_tab_details');
      $this->addTab('form_details', array(
          'label'     => 'General',
          'title'     => 'General Information',
          'content'   => $block->toHtml(),
      ));
      
      $this->addTab('text_section', array(
          'label'     => 'Misc / Custom Css',
          'title'     => 'Misc / Custom Css',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'text','_current' => true)),
          'class'     => 'ajax',
      ));
      
      $this->addTab('images_section', array(
          'label'     => 'Images',
          'title'     => 'Images',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'images')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('body_section', array(
          'label'     => 'Base Styles',
          'title'     => 'Base Styles',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'body')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('account_section', array(
          'label'     => 'Account Page',
          'title'     => 'Account Page Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'account')),
          'class'     => 'ajax',
      ));

      $this->addTab('blocks_section', array(
          'label'     => 'Blocks',
          'title'     => 'Blocks Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'blocks')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('buttons_section', array(
          'label'     => 'Buttons',
          'title'     => 'Buttons Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'buttons')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('cart_section', array(
          'label'     => 'Cart Page',
          'title'     => 'Cart Page Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'cart')),
          'class'     => 'ajax',
      ));

      $this->addTab('checkout_section', array(
          'label'     => 'Checkout Page',
          'title'     => 'Checkout Page Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'checkout')),
          'class'     => 'ajax',
      ));

      $this->addTab('forms_section', array(
          'label'     => 'Forms',
          'title'     => 'Form Settings',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'forms')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('notifications_section', array(
          'label'     => 'Notifications',
          'title'     => 'Notification Settings',
         'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'notifications')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('product_section', array(
          'label'     => 'Product',
          'title'     => 'Product Page Settings',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'product')),
          'class'     => 'ajax',
      ));
      
      $this->addTab('navigation_section', array(
          'label'     => 'Navigation',
          'title'     => 'Navigation',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'navigation')),
          'class'     => 'ajax',
      ));

      $this->addTab('custom_navigation_section', array(
          'label'     => 'Custom Navigation Block',
          'title'     => 'Custom Navigation Block',
          'url'   => $this->getUrl('*/*/edittab',array('id'=>$id,'tab'=>'customnavigation')),
          'class'     => 'ajax',
      ));
      
     return parent::_prepareLayout();
  }
}

