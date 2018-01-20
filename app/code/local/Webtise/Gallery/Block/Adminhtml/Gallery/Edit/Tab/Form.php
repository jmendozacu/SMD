<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 10/06/2016
 * Time: 13:39
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $gallery =Mage::registry('current_gallery');
        $form = new Varien_Data_Form();
        $form->setDataObject($gallery);

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

        $fieldset->addField('show_title', 'select', array(
            'label'     => Mage::helper('gallery')->__('Show Title on Frontend'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'show_title',
            'index'     => 'show_title',
            'values'    => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions()
        ));

        $fieldset->addField('description', 'text', array(
            'label'     => Mage::helper('gallery')->__('Description'),
            'required'  => false,
            'name'      => 'description',
            'index'     => 'description'
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('gallery')->__('Sort Order'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'sort_order',
            'index'     => 'sort_order',
            'after_element_html' => '<p class="nm"><small>If there is more than one Gallery on a single page this will be used to set the order of appearance.</small></p>'
        ));

        $fieldset->addField('gallery_class', 'text', array(
            'label'     => Mage::helper('gallery')->__('Gallery Html class'),
            'required'  => false,
            'name'      => 'gallery_class',
            'index'     => 'gallery_class',
            'after_element_html' => '<p class="nm"><small>Handy to show and hide based on a mobile/desktop class.</small></p>'
        ));

        $configSettings = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array(
                'add_widgets'               => true,
                'add_variables'             => true,
                'add_images'                => true,
                'files_browser_window_url'  => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            ));

        $fieldset->addField('gallery_html', 'editor', array(
            'label'                 => Mage::helper('gallery')->__('Gallery Html'),
            'required'              => false,
            'width'                 => '500px',
            'name'                  => 'gallery_html',
            'index'                 => 'gallery_html',
            'config'                => $configSettings,
            'wysiwyg'               => true,
            'after_element_html'    => '<p class="nm"><small>Can be used to add HTML to your gallery. Not yet available on a banner level but useful for static banenrs.</small></p>'
        ));

        $displayFieldset = $form->addFieldset('display_info',
            array(
                'legend' => Mage::helper('gallery')->__('Display Properties'),
                'class' => 'fieldset-wide',
            )
        );

        $displayFieldset->addField('block_reference', 'select', array(
            'label'     => Mage::helper('gallery')->__('Block Reference'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'block_reference',
            'index'     => 'block_reference',
            'values'    => Mage::getModel('gallery/source_block_reference')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Not applicable for custom pages.</small></p>'
        ));

        $display = $displayFieldset->addField('display_type', 'select', array(
            'label'     => Mage::helper('gallery')->__('Display Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'display_type',
            'index'     => 'display_type',
            'values'    => Mage::getModel('gallery/source_display_types')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Not applicable for custom pages.</small></p>'
        ));

        $items = ($gallery->getCarouselItems() ? Mage::registry('current_gallery')->getCarouselItems() : '1');

        $carouselItems = $displayFieldset->addField('carousel_items', 'text', array(
            'label'     => Mage::helper('gallery')->__('Visible Images on carousel'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'carousel_items',
            'index'     => 'carousel_items',
            'default'   => $items,
            'after_element_html' => '<p class="nm"><small>Not applicable for custom pages.</small></p>'
        ));

        $genericUrl = $displayFieldset->addField('is_generic_url', 'select', array(
            'label'     => Mage::helper('gallery')->__('Generic Url'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'is_generic_url',
            'index'     => 'is_generic_url',
            'values'    => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
            'after_element_html' => '<p class="nm"><small>Does every image click through to one url?</small></p>'
        ));

        $items = ($gallery->getCarouselItems() ? Mage::registry('current_gallery')->getCarouselItems() : '1');

        $url = $displayFieldset->addField('generic_url', 'text', array(
            'label'     => Mage::helper('gallery')->__('Url'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'generic_url',
            'index'     => 'generic_url',
            'default'   => $items,
            'after_element_html' => '<p class="nm"><small>Url for all images to click through to.</small></p>'
        ));

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($display->getHtmlId(), $display->getName())
            ->addFieldMap($carouselItems->getHtmlId(), $carouselItems->getName())
            ->addFieldMap($genericUrl->getHtmlId(), $genericUrl->getName())
            ->addFieldMap($url->getHtmlId(), $url->getName())
            ->addFieldDependence(
                $carouselItems->getName(),
                $display->getName(),
                'carousel'
            )
            ->addFieldDependence(
                $url->getName(),
                $genericUrl->getName(),
                '1'
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