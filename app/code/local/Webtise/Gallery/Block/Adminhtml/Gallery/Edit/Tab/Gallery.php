<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Gallery extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_gallery'));

        $fieldset = $form->addFieldset('info',
            array(
                'legend' => Mage::helper('gallery')->__('Gallery Images'),
                'class' => 'fieldset-wide',
            )
        );

        $attributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            $attribute->setEntity(Mage::getResourceModel('gallery/gallery'));
        }

        $this->_setFieldset($attributes, $fieldset, array());

        $formValues = Mage::registry('current_gallery')->getData();

        $form->addValues($formValues);

        $form->setFieldNameSuffix('gallery');

        $this->setForm($form);
    }

    protected function _prepareLayout() {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('gallery/adminhtml_gallery_renderer_fieldset_element')
        );
    }

    protected function _getAdditionalElementTypes(){
        return array(
            'gallery'    => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_gallery'),
            'file'    => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_file'),
            'image' => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_image'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }

    public function getGallery() {
        return Mage::registry('current_gallery');
    }
}