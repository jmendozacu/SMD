<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Form {

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
        $fieldset3 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Cart', 'cart');
        Mage::helper('flexitheme/theme')->createHeading($fieldset3, 'Total', 'cart-total');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Box', 'cart-total-box', '.cart .totals', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Strong text', 'cart-total-text-strong', '.cart .totals tfoot th strong, .cart .totals tfoot td strong', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Text', 'cart-total-text', '.cart .totals td', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createHeading($fieldset3, 'Tax', 'cart-total-tax');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset3, 'Tax Breakdown', 'cart-total-tax', '.cart .totals tr.summary-details td', $data);

        $fieldset4 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Cart Boxes', 'cart-box');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Box', 'cart-box', '.cart .discount, .cart .shipping', $data);
        Mage::helper('flexitheme/theme')->createHeading($fieldset4, 'Discount', 'cart-box-discount');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Discount Header', 'cart-box-discount-header', '.cart .discount h2', $data);
        Mage::helper('flexitheme/theme')->createHeading($fieldset4, 'Shipping', 'cart-box-shipping');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset4, 'Shipping Header', 'cart-box-shipping-header', '.cart .shipping h2', $data);

        //general datatables.
        $fieldset5 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'General data table', 'data-table');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Headers', 'data-table-header', '.data-table thead th', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Odd Rows', 'data-table-odd', '.data-table .odd', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Even Rows', 'data-table-even', '.data-table .even', $data);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Remove', 'data-table-remove', '.btn-remove2,.btn-remove', $data, false, true, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Footer', 'data-table-footer', '.data-table tfoot tr,.data-table tfoot tr.first td', $data, false, true, true);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset5, 'Pager', 'grid-odd-pager', '.pager', $data);
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}


