<?php

class Epicor_FlexiTheme_Block_Adminhtml_Theme_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form {

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
        
        $theme_helper = Mage::helper('flexitheme/theme');
        /* @var $theme_helper Epicor_FlexiTheme_Helper_Theme */
        
        $skin_folder = $theme_helper->safeString($theme['theme_name']);
        
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('imgs_');
        $form->setFieldNameSuffix('imgs');


        $this->setForm($form);

        $fieldset = Mage::helper('flexitheme/theme')->createFieldSet($form, 'Images', 'images_items_form');


        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Logo', 'logo_imgs');

        
        $fieldset->addField('logo', 'image_replace', array(
              'label'       => 'Logo',
              'name'        => 'logo.gif',
              'image'       => 'logo.gif',
              'name_suffix' => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
        ));
        
        $fieldset->addField('favicon', 'image_replace', array(
              'label'       => 'Fav Icon',
              'name'        => 'favicon.ico',
              'image'       => 'favicon.ico',
              'name_suffix' => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
              'note'        => 'Allowed file types: ICO, PNG, GIF, JPEG, APNG, SVG.<br>Not all browsers support all these formats!<br>Convert your image to ICO format first with <a href="http://www.favicon.cc" target="_blank">http://www.favicon.cc</a>'
        ));
        
        $fieldset->addField('logo_email', 'image_replace', array(
              'label'     => 'Email Logo',
              'name'      => 'logo_email.gif',
              'image'      => 'logo_email.gif',
              'name_suffix'   => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
        ));
        
        $fieldset->addField('logo_print', 'image_replace', array(
              'label'     => 'Print Logo',
              'name'      => 'logo_print.gif',
              'image'      => 'logo_print.gif',
              'name_suffix'   => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
        ));
        
        
        Mage::helper('flexitheme/theme')->createHeading(
                $fieldset, 'Pagination Tool Bar', 'pagination_imgs');
        
        $fieldset->addField('i_asc_arrow', 'image_replace', array(
              'label'     => 'Ascending Arrow',
              'name'      => 'i_asc_arrow.gif',
              'image'      => 'i_asc_arrow.gif',
              'name_suffix'   => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
        ));
        
        $fieldset->addField('i_desc_arrow', 'image_replace', array(
              'label'     => 'Descending Arrow',
              'name'      => 'i_desc_arrow.gif',
              'image'      => 'i_desc_arrow.gif',
              'name_suffix'   => $form->getFieldNameSuffix(),
              'skin_folder' => $skin_folder,
        ));
        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}