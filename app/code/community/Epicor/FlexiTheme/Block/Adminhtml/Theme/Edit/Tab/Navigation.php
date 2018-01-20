<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Navigation extends Mage_Adminhtml_Block_Widget_Form {

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

        // Breadcrumbs
         $breadcrumbs = Mage::helper('flexitheme')->createFieldSet($form, 'Breadcrumbs', 'breadcrumbs');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Breadcrumb Container', 'breadcrumbs', '.breadcrumbs', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Breadcrumb', 'breadcrumb', '.breadcrumbs li strong', $data, true, true, true);
        
        
        // Quick Access Navigation
         $breadcrumbs = Mage::helper('flexitheme')->createFieldSet($form, 'Quick Access Navigation', 'quick_access');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Container', 'quick_access', '.header .quick-access', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Welcome Message', 'quick_access_welcome', '.header .welcome-msg', $data);
        
         Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Nav Container', 'quick_access_links', '.header .quick-access .links', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Item', 'quick_access_item', '.header .quick-access .links li', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Item', 'quick_access_item_text', '.header .quick-access .links li a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Item Hover', 'quick_access_item_hover', '.header .quick-access .links li:hover', $data, false, true, true);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $breadcrumbs, 'Quick Access Item Hover', 'quick_access_item_hover_text', '.header .quick-access .links li a:hover', $data, true, false, false);
        
        
        
         $fieldset3 = Mage::helper('flexitheme')->createFieldSet($form, 'Top Container', 'nav_form');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Top Nav Background', 'nav-container', '.nav-container', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Top Nav Foreground', 'nav', '#nav', $data, false, true, true);

        
        $fieldset = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Top Navigation', 'top_nav_items_form');


        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Nav Item', 'nav_li');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item', 'nav_li', '#nav li', $data, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item', 'nav_li_a', '#nav li a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item Hover / Active', 'nav_li_a_hover', '#nav li a:hover, #nav li.over a, #nav li.active a', $data, true, false, false);
        


        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Sub Nav Container', 'nav_li_ul');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Container', 'nav_li_ul', '#nav li ul', $data, false);
        
        

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Sub Nav Item', 'nav_li_ul_li');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item', 'nav_li_ul_li', '#nav li ul li, #nav li li.last', $data, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Last Item', 'nav_li_ul_li_last', '#nav li li.last', $data, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item', 'nav_li_ul_li_a', '#nav li ul li a,#nav li.active ul li a, #nav li.over ul li a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item Hover / Active', 'nav_li_ul_li_a_hover', '#nav li ul li a:hover, #nav li ul li.over a, #nav li ul li.active a', $data, true, true, false);
        

        
        
        
        $fieldset = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Side Navigation', 'side_nav_items_form');

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Nav Base', 'side_nav');
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Background', 'side_nav', '.side-nav-block', $data, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Headng', 'side_nav_h2', '.side-nav-block h2', $data);

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Nav Item', 'side_nav_li');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item Container', 'side_nav_li', '.fullsidenav li', $data, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item Container Parent', 'side_nav_li_parent', '.fullsidenav li.parent', $data, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item', 'side_nav_li_a', '.fullsidenav li a', $data);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Nav Item Hover / Active', 'side_nav_li_a_hover', '.fullsidenav li a:hover, .fullsidenav li.active a', $data);
        


        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Sub Nav Container', 'side_nav_li_ul');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Container', 'side_nav_li_ul', '.fullsidenav li ul', $data, false);
        
        

        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Sub Nav Item', 'side_nav_li_ul_li');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item', 'side_nav_li_ul_li', '.fullsidenav li ul li, .fullsidenav li li.last', $data, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Last Item', 'side_nav_li_ul_li_last', '.fullsidenav li li li.last', $data, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item', 'side_nav_li_ul_li_a', '.fullsidenav li.active li a,.fullsidenav li.active li.active li a,.fullsidenav li.active li.active li.active li a,.fullsidenav li.active li.active li.active li.active li a', $data);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Sub Nav Item Hover / Active', 'side_nav_li_ul_li_a_hover', '.fullsidenav li.active li a:hover,.fullsidenav li.active li.active a,.fullsidenav li.active li.active li a:hover,.fullsidenav li.active li.active li.active a,.fullsidenav li.active li.active li.active li a:hover,.fullsidenav li.active li.active li.active li.active a,.fullsidenav li.active li.active li.active li.active li a:hover,.fullsidenav li.active li.active li.active li.active li.active a', $data);
        

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
