<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Navigation_Edit_Tab_Links_Add extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $block_id = Mage::getSingleton('adminhtml/session')->getTempBlockId();
        
        if(Mage::registry('layout_block_data'))
            $block_id = Mage::registry('layout_block_data')->getId();
            
        $onClickArgs=array(
          'id'=>$block_id  
        );
        $url = Mage::getUrl('*/*/addlink',$onClickArgs);
        $onclick = "submitAndReloadArea(form_tabs_form_links_content,'$url')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->getButtonHtml('Add Link', $onclick, 'save');

        //$this->setChild('header', $button);

        $fieldset = $form->addFieldset('add_block_links', array(
            'legend' => Mage::helper('adminhtml')->__('Create New Link'),
            'header_bar' => $button
                ));

        $fieldset->addField('display_title', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Display Title'),
            'name' => 'display_title'
        ));
        
        $fieldset->addField('tooltip_title', 'text', array(
            'label' => Mage::helper('adminhtml')->__('ToolTip Title'),
            'name' => 'tooltip_title'
        ));
        
        $fieldset->addField('link_identifier', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Link Identifier'),
            'name' => 'link_identifier'
        ));
        
        $cms_pages = Mage::getModel('cms/page')->getCollection()->toOptionArray();
        $cms_pages = array_merge(array(array('value' => '', 'label' => 'Custom Url')), $cms_pages);

        
        $fieldset->addField('cms_page_id', 'select', array(
            'label' => Mage::helper('adminhtml')->__('CMS Page'),
            'values' => $cms_pages,
            'name' => 'cms_page_id'
        ));
        
        $fieldset->addField('link_url', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Custom Url'),
            'name' => 'link_url'
        ));
        
        return parent::_prepareForm();
    }

}


