<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Body extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        /* @var $theme ArrayObject */
        /* @var $data ArrayObject */
        if (Mage::getSingleton('adminhtml/session')->getThemeData()) {
            $theme = Mage::getSingleton('adminhtml/session')->getThemeData();
            Mage::getSingleton('adminhtml/session')->getThemeData(null);
        } elseif (Mage::registry('theme_data')) {
            $theme = Mage::registry('theme_data')->getData();
        } else {
            $theme = array();
        }

        if (isset($theme['entity_id'])) {
            $data = Mage::helper('flexitheme/theme')->loadThemeDesignData($theme['entity_id']);
        }

        
        $form = new Varien_Data_Form();
        
        $form->setHtmlIdPrefix('css_');
        $form->setFieldNameSuffix('css');


        $this->setForm($form);
        $fieldset1 = Mage::helper('flexitheme')->createFieldSet($form, 'Base', 'body_background_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Body', 'body', 'body', $data, false, true, true);

        $fieldset2 = Mage::helper('flexitheme')->createFieldSet($form, 'Header', 'header_background_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Header Background', 'header-container', '.header-container', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Header Foreground', 'header', '.header', $data, false, true, true);


       

        $fieldset4 = Mage::helper('flexitheme')->createFieldSet($form, 'Main', 'main_background_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Main Background', 'main-container', '.main-container', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Main Foreground', 'main', '.main', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Left Column', 'left-col', '.col-left', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Main Column', 'main-col', '.col-main', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Right Column', 'right-col', '.col-right', $data, false, true, true);


        $fieldset5 = Mage::helper('flexitheme')->createFieldSet($form, 'Footer', 'footer_background_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Footer Background', 'footer-container', '.footer-container', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Footer Foreground', 'footer', '.footer', $data);
         Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Footer Links', 'footer-links', '.footer li a', $data,true,false,false);
         Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Footer Links', 'footer-links', '.footer li', $data,false,true,true);
        
        
        $fieldset6 = Mage::helper('flexitheme')->createFieldSet($form, 'Default Text Styles', 'productlist_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Default Font', 'body_font', 'body', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Link Font', 'a', 'a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Paragraph Padding', 'para_padding', '.main .page-title, .main .std', $data, false, false, true);

        
        
        
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Logo', 'logo_pos', '.header a.logo', $data, false, false, true);
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}