<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Blocks extends Mage_Adminhtml_Block_Widget_Form {

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

        $fieldset10 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Blocks', 'blocks');

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Box', 'block-box', '.main .block ', $data);
        Mage::helper('flexitheme/theme')->createHeading($fieldset10, 'Title', 'block-title');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Title', 'block-title', '.block .block-title strong', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Title', 'block-title', '.block .block-title', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Basket Title', 'block-cart-title', '.block-cart .block-title strong', $data);

        Mage::helper('flexitheme/theme')->createHeading($fieldset10, 'Content', 'block-content');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Sub Title', 'block-content-subtitle', '.block .block-subtitle ', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Links', 'block-content-links', '.block .block-content a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Content', 'block-content', '.block .block-content,.block-cart .subtotal ', $data);

        Mage::helper('flexitheme/theme')->createHeading($fieldset10, 'Actions', 'block-actions');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Action Container', 'block-buttons-container, ', '.block .actions ', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Action Buttons', 'block-buttons-buttons', '.block .actions button.button span', $data);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Text Input', 'block-input-text', '.block input.input-text,.block textarea', $data);

        Mage::helper('flexitheme/theme')->createHeading($fieldset10, 'Lists', 'block-lists');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'List Items', 'block-list-default', '.block li', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'List Odd items', 'block-list-odd', '.block li.odd ', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'List Even', 'block-list-even', '.block li.even', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Edit Link', 'block-list-edit', '.block li .btn-edit', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Product Image', 'block-list-product', '.block li .product-image', $data, false, false, true);




        $form->setValues($data);
        return parent::_prepareForm();
    }

}


