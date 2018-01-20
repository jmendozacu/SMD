<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:43
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $galleryTag =Mage::registry('current_gallery_tag');

        $form = new Varien_Data_Form(array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl(
                    '*/*/save',
                    array(
                        'id' => $this->getRequest()->getParam('id'),
                        'store' => $this->getRequest()->getParam('store')
                    )
                ),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $form->setUseForm($form);
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

        $fieldset->addField('image', 'image', array(
            'label'     => Mage::helper('gallery')->__('Image'),
            'required'  => false,
            'name'      => 'image',
            'index'     => 'image',
            'after_element_html' => '<p class="nm"><small>If shown as swatch, this image is used</small></p>'
        ));

        $formValues = Mage::registry('current_gallery_tag')->getData();
        $form->addValues($formValues);
        $form->setFieldNameSuffix('gallery_tag');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes(){
        return array(
            'file'    => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_file'),
            'image' => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_image'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }

    protected function _getGalleryTag()
    {
        if (!$this->hasData('gallery_tag')) {
            $tag = Mage::registry('current_gallery_tag');

            if (!$tag instanceof Webtise_Gallery_Model_Gallery_Tag) {
                $tag = Mage::getModel('gallery/gallery_tag');
            }

            $this->setData('gallery_tag', $tag);
        }

        return $this->getData('gallery_tag');
    }
}