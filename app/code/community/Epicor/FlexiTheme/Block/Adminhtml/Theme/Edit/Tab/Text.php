<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Text extends Mage_Adminhtml_Block_Widget_Form {

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

        if(isset($theme['entity_id']))
        {
            $data = Mage::helper('flexitheme/theme')->loadThemeDesignData($theme['entity_id']);
        }
        
        $form = new Varien_Data_Form();


        $form->setHtmlIdPrefix('css_');
        $form->setFieldNameSuffix('css');

        $this->setForm($form);
        $data['custom_css'] = $data['custom']['css_properties']['value'];
        $fieldset=Mage::helper('flexitheme')->createFieldSet($form,'Custom Skin CSS','custom_css_form');
        $fieldset->addField('custom_css', 'textarea', array(
            'label' => Mage::helper('adminhtml')->__('Custom CSS'),
            'name' => 'custom[css][value]',
            'index' => 'custom_css'
        ));

        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset, 'Page Titles', 'page_titles', '.page-title h1, .page-title h2', $data);
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}