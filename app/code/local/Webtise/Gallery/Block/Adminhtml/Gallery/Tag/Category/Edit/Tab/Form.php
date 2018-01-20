<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 10/06/2016
 * Time: 13:39
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $galleryTag =Mage::registry('current_gallery_tag_category');
        $form = new Varien_Data_Form();
        $form->setDataObject($galleryTag);

        $fieldset = $form->addFieldset('info',
            array(
                'legend' => Mage::helper('gallery')->__('Frontend Properties'),
                'class' => 'fieldset-wide',
            )
        );

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('gallery')->__('Enabled'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'status',
            'index'     => 'status',
            'values'    => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('gallery')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
            'index'     => 'title'
        ));

        $fieldset->addField('frontend_display', 'select', array(
            'label'     => Mage::helper('gallery')->__('Frontend Display'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'frontend_display',
            'index'     => 'frontend_display',
            'values'    => Mage::getModel('gallery/source_frontend_display')->getAllOptions()
        ));

        $formValues = Mage::registry('current_gallery_tag_category')->getData();
        $form->addValues($formValues);
        $form->setFieldNameSuffix('gallery_tag_category');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes(){
        return array(
            'file'    => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_file'),
            'image' => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_image'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }

    public function getGallery() {
        return Mage::registry('current_gallery_tag_category');
    }
}