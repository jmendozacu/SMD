<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Account extends Mage_Adminhtml_Block_Widget_Form {

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

        $fieldset1 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Account Navigation', 'acc-nav');
        Mage::helper('flexitheme/theme')->createHeading($fieldset1, 'Header', 'acc-nav-header');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Header', 'acc-nav-header', '.block-account div.block-title', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Header', 'acc-nav-header', '.block-account div.block-title strong', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createHeading($fieldset1, 'Content', 'acc-nav-content');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Content', 'acc-nav-content', '.block-account .block-content', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Current', 'acc-nav-content-current', '.block-account .block-content li.current', $data, true, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Navigation Items', 'acc-nav-content-item', '.block-account .block-content li a', $data, true, false, true);


        $fieldset2 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Account Boxes', 'acc-box');
        Mage::helper('flexitheme/theme')->createHeading($fieldset1, 'Header', 'acc-box-header');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Header', 'acc-box-header', '.dashboard .box-account .box-head h2', $data);

        Mage::helper('flexitheme/theme')->createHeading($fieldset1, 'Content', 'acc-box');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Box', 'acc-box', '.box-account', $data, true, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Links', 'acc-box-link', '.box-account a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Reorder Link', 'acc-box-reorder', '.box-account a.link-reorder', $data, true, false, false);
    



        $form->setValues($data);
        return parent::_prepareForm();
        
    }
}
