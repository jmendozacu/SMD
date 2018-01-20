<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Checkout extends Mage_Adminhtml_Block_Widget_Form {

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

       
        //Checkout page.
        $fieldset6 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Checkout Page', 'checkout');
        Mage::helper('flexitheme/theme')->createHeading($fieldset6, 'Active', 'checkout-active');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Active Title', 'checkout-title-active', '.opc .active .step-title', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Active Title Number', 'checkout-title-active-number', '.opc .active .step-title .number', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Active Title Text', 'checkout-title-active-h2', '.opc .active .step-title h2, .info-set h2.legend', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Active Continue Button', 'checkout-active-button', '.opc .step button.button span', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Step Content', 'checkout-active', '.opc .step .form-list label,.opc .step .sp-methods label, .info-set h3, .info-set h4', $data, true, false, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Step Content', 'checkout-active', '.opc .step', $data, false, true, true);

        Mage::helper('flexitheme/theme')->createHeading($fieldset6, 'Inactive', 'checkout-inactive');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Inactive Title', 'checkout-title-inactive', '.opc .step-title', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Inactive Title Number', 'checkout-title-inactive-number', '.opc .step-title .number', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Inactive Title Test', 'checkout-title-inactive-h2', '.opc .step-title h2', $data);
        //right hand progress
        //central blocks
        Mage::helper('flexitheme/theme')->createHeading($fieldset6, 'Progress', 'checkout-progress');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Progress Complete Header', 'checkout-progress-complete-header', '.block-progress dt.complete', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Progress Complete Content Links', 'checkout-progress-complete-content-links', '.block-progress dt.complete a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Progress Complete Content', 'checkout-progress-complete-content', '.block-progress dd.complete', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Progress Incomplete', 'checkout-progress', '.block-progress dt', $data);


        $form->setValues($data);
        return parent::_prepareForm();
    }

}


