<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Customnavigation extends Mage_Adminhtml_Block_Widget_Form {

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

        // Quick Access Navigation
         $breadcrumbs = Mage::helper('flexitheme')->createFieldSet($form, 'Header Custom Navigation Block', 'customtopnav_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Container', 'customtopnav', '.header .customtopnav', $data, false, true, true);
        
         Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Container', 'customtopnav_links', '.header .customtopnav .links', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Item', 'customtopnav_item', '.header .customtopnav .links li', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Item', 'customtopnav_item_text', '.header .customtopnav .links li a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Item Hover', 'customtopnav_item_hover', '.header .customtopnav .links li:hover', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Header Custom Navigation Item Hover', 'customtopnav_item_hover_text', '.header .customtopnav .links li a:hover', $data, true, false, false);
        
        
        
        $fieldset = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Side Custom Navigation', 'customsidenav_form');

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Side Custom Navigation Base', 'customsidenav-block');
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Background', 'side_nav', '.customsidenav-block', $data, false);

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Side Custom Navigation Item', 'customsidenav_li');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Side Custom Navigation Item Container', 'customsidenav_li', '.customsidenav li', $data, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Side Custom Navigation Item', 'customsidenav_li_a', '.customsidenav li a', $data);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Side Custom Navigation Item Hover / Active', 'customsidenav_li_a_hover', '.customsidenav li a:hover, .customsidenav li.active a', $data);
        

        // Quick Access Navigation
         $breadcrumbs = Mage::helper('flexitheme')->createFieldSet($form, 'Footer Custom Navigation Block', 'footercustomtopnav_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Container', 'footercustomtopnav', '.footer .customtopnav', $data, false, true, true);
        
         Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Container', 'footercustomtopnav_links', '.footer .customtopnav .links', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Item', 'footercustomtopnav_item', '.footer .customtopnav .links li', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Item', 'footercustomtopnav_item_text', '.footer .customtopnav .links li a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Item Hover', 'footercustomtopnav_item_hover', '.footer .customtopnav .links li:hover', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Footer Custom Navigation Item Hover', 'footercustomtopnav_item_hover_text', '.footer .customtopnav .links li a:hover', $data, true, false, false);
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}