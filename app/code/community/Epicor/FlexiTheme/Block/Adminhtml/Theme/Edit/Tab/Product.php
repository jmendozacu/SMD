<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form {

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

        $fieldset6 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Old Price', 'txt-old-price');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset6, 'Old Price', 'txt-old-price', '.old-price .price, .regular-price .price, .price-box .price, .price-box .price-label, .price-from .price-label, .price-to .price-label', $data, true, false, false);

        $fieldset7 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Special Price', 'txt-special-price');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset7, 'Special Price', 'txt-special-price', '.special-price .price', $data, true, false, false);

        $fieldset8 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Pager', 'pl-pager');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset8, 'Pager', 'pl-pager', '.toolbar .pager', $data);

        $fieldset9 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Sorter', 'pl-sorter');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset9, 'Sorter', 'pl-sorter', '.toolbar .sorter', $data);
        

        $fieldset10 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Product Details', 'pl-links');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Product Details add to', 'pl-links', '.product-view .product-shop .add-to-links a', $data, true, false, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Additional Info Wrapper', 'product-collateral', '.product-view .product-collateral', $data, false);

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset10, 'Additional Info Box', 'box-collateral', '.product-view .box-collateral', $data, false);

        //.products-list li.item
        $fieldset11 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Product List', 'pl-list');
        Mage::helper('flexitheme/theme')->createHeading($fieldset11, 'Product List Default', 'pl-list');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product List Default', 'pl-list', '.products-list li.item', $data);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product Name', 'def-product-name', '.products-list .product-name a', $data, true, false, false);
        
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Old Price', 'def-old-price', 'li.item .old-price .price,li.item .regular-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Special Price', 'def-special-price', 'li.item .special-price .price', $data, true, false, false);

        Mage::helper('flexitheme/theme')->createHeading($fieldset11, 'Product List Odd', 'pl-list-odd');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product Name Odd', 'odd-product-name', '.products-list li.odd .product-name a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product List Odd', 'pl-list-odd', 'ol.products-list li.odd', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Old Price', 'odd-old-price', 'li.odd .old-price .price,li.odd .regular-price .price ', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Special Price', 'odd-special-price', 'li.odd .special-price .price', $data, true, false, false);

        Mage::helper('flexitheme/theme')->createHeading($fieldset11, 'Product List Even', 'pl-list-even');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product Name Even', 'even-product-name', '.products-list li.even .product-name a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Product List Even', 'pl-list-even', 'ol.products-list li.even', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Old Price', 'even-old-price', 'li.even .old-price .price,li.even .regular-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset11, 'Special Price', 'even-special-price', 'li.even .special-price .price', $data, true, false, false);

        //product list grid.
        $fieldset12 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Product Grid', 'pl-grid');
        Mage::helper('flexitheme/theme')->createHeading($fieldset12, 'Product Grid Default', 'pl-grid-def');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Name', 'grid-product-name-def', '.products-grid .product-name a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Grid Default', 'pl-grid-def', '.category-products .products-grid', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Old Price', 'grid-old-price', '.category-products .products-grid .old-price .price, .category-products .products-grid .regular-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Special Price', 'grid-special-price', '.category-products .products-grid .special-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Links', 'grid-def-links', '.category-products ul a', $data, true, false, false);

        Mage::helper('flexitheme/theme')->createHeading($fieldset12, 'Product Grid Even', 'pl-grid-even');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Name Even', 'grid-product-name-even', '.products-grid .even .product-name a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Grid Even', 'pl-grid-even', '.category-products .even', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Old Price', 'grid-even-old-price', '.category-products .even .old-price .price, .category-products .even .regular-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Special Price', 'grid-even-special-price', '.category-products .even .special-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Links', 'grid-even-links', '.category-products ul.even a', $data, true, false, false);


        Mage::helper('flexitheme/theme')->createHeading($fieldset12, 'Product Grid Odd', 'pl-grid-odd');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Name Odd', 'grid-product-name-odd', '.products-grid .odd .product-name a', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Product Grid Odd', 'pl-grid-odd', '.category-products ul.odd', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Old Price', 'grid-odd-old-price', '.category-products ul.odd .old-price .price, .category-products ul.odd .regular-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Special Price', 'grid-odd-special-price', '.category-products ul.odd.special-price .price', $data, true, false, false);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset12, 'Links', 'grid-odd-links', '.category-products ul.odd a', $data, true, false, false);


        $form->setValues($data);
        return parent::_prepareForm();
    }

}
