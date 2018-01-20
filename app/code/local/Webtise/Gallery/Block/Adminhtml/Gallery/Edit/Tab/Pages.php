<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 22/06/2016
 * Time: 11:24
 **/

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Pages extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $gallery = Mage::registry('current_gallery');
        $form = new Varien_Data_Form();
        $form->setDataObject($gallery);

        $fieldset = $form->addFieldset('info',
            array(
                'legend' => Mage::helper('gallery')->__('CMS Pages'),
                'class' => 'fieldset-wide',
            )
        );

        $fieldset->addField('show_on_cms', 'select', array(
            'label'     => Mage::helper('gallery')->__('Show Gallery On CMS Pages'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'show_on_cms',
            'index'     => 'show_on_cms',
            'values'    => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Do you want to show this Gallery on cms pages?</small></p>'
        ));

        $fieldset->addField('cms_type', 'select', array(
            'label'     => Mage::helper('gallery')->__('Which CMS Pages'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'cms_type',
            'index'     => 'cms_type',
            'values'    => Mage::getModel('gallery/source_cms_type')->getAllOptions()
        ));

        $fieldset->addField('pages', 'multiselect', array(
            'label'     => Mage::helper('gallery')->__('Pages to show on'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'pages[]',
            'index'     => 'pages',
            'values'    => Mage::getModel('gallery/source_pages')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Choose which pages you want this gallery to show on.</small></p>'
        ));

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap('show_on_cms', 'show_on_cms')
            ->addFieldMap('cms_type', 'cms_type')
            ->addFieldMap('pages', 'pages')
            ->addFieldDependence(
                'cms_type',
                'show_on_cms',
                '1'
            )
            ->addFieldDependence(
                'pages',
                'show_on_cms',
                '1'
            )
            ->addFieldDependence(
                'pages',
                'cms_type',
                'specific_page'
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

    public function getGallery() {
        return Mage::registry('current_gallery');
    }
}