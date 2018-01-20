<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Forms extends Mage_Adminhtml_Block_Widget_Form {

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

       
        $fieldset7 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Login Page', 'login');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Heading', 'login-heading', '.account-login .content h2', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Content', 'login-box', '.account-login .content', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Content Label', 'login-box-labels', '.account-login label', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Footer', 'login-footer', '.account-login .buttons-set', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Buttons', 'login-buttons', '.account-login button.button span', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Box Links', 'login-links', '.account-login .buttons-set a', $data);

        $fieldset8 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Contact us Page', 'contact');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Box Heading', 'contact-heading', '#contactForm .fieldset .legend', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Box Content', 'contact-box', '#contactForm .fieldset', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Box Content Label', 'contact-box-labels', '#contactForm .fieldset label', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Box Footer', 'contact-footer', '#contactForm .buttons-set', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Box Buttons', 'contact-buttons', '#contactForm button.button span,#contactForm .buttons-set a', $data);
        
        $fieldset11 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Errors Page', 'errors');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Errors Form', 'errors-form', '.errors-page .fieldset', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Errors Form', 'errors-label', '.errors-page .fieldset label', $data);

        $form->setValues($data);
        return parent::_prepareForm();
    }

}


