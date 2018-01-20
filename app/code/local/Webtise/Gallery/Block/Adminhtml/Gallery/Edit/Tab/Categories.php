<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_gallery'));

        $fieldset = $form->addFieldset('info',
            array(
                'legend' => Mage::helper('gallery')->__('Category Display'),
                'class' => 'fieldset-wide',
            )
        );

        $fieldset->addField('show_on_categories', 'select', array(
            'label'     => Mage::helper('gallery')->__('Show Gallery On Category Pages'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'show_on_categories',
            'index'     => 'show_on_categories',
            'values'    => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Do you want to show this Gallery on category pages?</small></p>'
        ));


        $fieldset->addField('category_type', 'select', array(
            'label'     => Mage::helper('gallery')->__('Which Categories'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'category_type',
            'index'     => 'category_type',
            'values'    => Mage::getModel('gallery/source_category_type')->getAllOptions()
        ));

        $fieldset->addType('categories', 'Webtise_Gallery_Block_Adminhtml_Gallery_Helper_Categories');

        $fieldset->addField('category_ids', 'categories', array(
            'label'     => Mage::helper('gallery')->__('Select Categories'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'category_ids',
            'index'     => 'category_ids'
        ));

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('show_on_categories', 'show_on_categories')
            ->addFieldMap('category_type', 'category_type')
            ->addFieldMap('category_ids', 'category_ids')
            ->addFieldDependence(
                'category_type',
                'show_on_categories',
                '1'
            )
            ->addFieldDependence(
                'category_ids',
                'show_on_categories',
                '1'
            )
            ->addFieldDependence(
                'category_ids',
                'category_type',
                'specific_category'
            )
        );

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
            'categories'    => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_categories'),
            'gallery'       => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_gallery'),
            'file'          => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_file'),
            'image'         => Mage::getConfig()->getBlockClassName('gallery/adminhtml_gallery_helper_image'),
            'textarea'      => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }

    public function getGallery() {
        return Mage::registry('current_gallery');
    }
}