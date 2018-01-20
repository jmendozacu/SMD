<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Notifications extends Mage_Adminhtml_Block_Widget_Form {

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

       
 
        $fieldset9 = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Messages', 'messages');
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset9, 'Notifications', 'message-notification', '.note-msg, .notice-msg', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset9, 'Success', 'message-success', '.success-msg', $data);
        Mage::helper('flexitheme/theme')->createBgFontBorder(
                $fieldset9, 'Error', 'message-error', '.error-msg', $data);

       


        $form->setValues($data);
        return parent::_prepareForm();
    }

}


