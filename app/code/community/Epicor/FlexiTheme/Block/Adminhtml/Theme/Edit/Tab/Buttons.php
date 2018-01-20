<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Buttons extends Mage_Adminhtml_Block_Widget_Form {

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

        $fieldset1 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Default Button', 'button');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Default Button', 'button_text', 'button.button span', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Default Button', 'button', 'button.button', $data, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset1, 'Default Button Hover', 'button_hover', 'button.button:hover', $data, false);

        $fieldset2 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Cart Buttons', 'btn-update');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset2, 'Cart Buttons', 'btn-update', '.cart-table .btn-continue span,.cart-table .btn-update span, .cart .discount button span,.cart .shipping button span', $data);

        $fieldset3 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Proceed Button', 'btn-proceed');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Proceed Button', 'btn-proceed', 'button.btn-proceed-checkout span', $data);

        $fieldset4 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Search Button', 'btn-search');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Search Button', 'btn-search', '.header .form-search button.button span', $data);

        $fieldset5 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Search Form', 'frm-search');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Search Form', 'frm-search', '.header .form-search', $data);

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
