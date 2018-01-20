<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Edit_Tab_Page extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $helper = Mage::helper('flexitheme/layout');
        /* @var $helper Epicor_FlexiTheme_Helper_Layout */
        
        if ($this->getPageType() != 'cms_page') {
            $data = $helper->getLayoutPageData($this->getLayoutId(), $this->getPageId(), $this->getPageType());
        } else {
            $data = $helper->getCmsPageData($this->getLayoutId(), $this->getCmsPageIdentifier());
        }

        $form = new Varien_Data_Form();
        $fieldset_name = $data['fieldset_name'];
        $field_prefix_name = $data['field_prefix_name'];
        $data[$field_prefix_name . '_layout_page_name'] = $data['page_name'];
        $data[$field_prefix_name . '_layout_page_type'] = $this->getPageType();

        $form->setHtmlIdPrefix('page_' . $field_prefix_name . '_');
        $form->setFieldNameSuffix('page[' . $field_prefix_name . ']');

        $this->setForm($form);
        $fieldset = $form->addFieldset($fieldset_name . '_form', array('legend' => Mage::helper('flexitheme')->__($data['page_name'] . ' Layout')));
        $fieldset->addType('sortable_list', 'Epicor_FlexiTheme_Lib_Varien_Data_Form_Element_Sortablelist');

        $fieldset->addField($field_prefix_name . '_layout_page_id', 'hidden', array(
            'name' => 'layout_page_id',
        ));
        
        $fieldset->addField($field_prefix_name . '_layout_page_name', 'hidden', array(
            'name' => 'layout_page_name',
        ));

        $fieldset->addField($field_prefix_name . '_layout_page_type', 'hidden', array(
            'name' => 'page_type',
        ));

        $fieldset->addField($field_prefix_name . '_page_id', 'hidden', array(
            'name' => 'page_id',
        ));
        
        $fieldset->addField($field_prefix_name . '_cms_page_identifier', 'hidden', array(
            'name' => 'cms_page_identifier',
        ));

        $template_options = Mage::getModel('flexitheme/layout_template')->toOptionArray();
        if ($this->getPageType() != 'def_page') {
            $fieldset->addField($field_prefix_name . '_template_id', 'select', array(
                'name' => 'template_id',
                'label' => Mage::helper('flexitheme')->__('Layout'),
                'required' => true,
                'values' => $template_options,
            ));
        } else {
            $data[$field_prefix_name . '_layout_template_id'] = $template_options[count($template_options)-1]['value'];
            $fieldset->addField($field_prefix_name . '_layout_template_id', 'hidden', array(
                'name' => 'template_id',
            ));
        }
        foreach ($this->getSections() as $section) {

            $fieldset->addField($section->getSectionName(), 'sortable_list', array(
                'name' => $section->getSectionName(),
                'section' => $section->getSectionName(),
                'label' => Mage::helper('flexitheme')->__($section->getSectionName()),
                'group' => $field_prefix_name . '_columns',
                'values' => @$data['Section_' . $section->getSectionName()],
            ));
        }

        $fieldset->addField('available', 'sortable_list', array(
            'name' => 'available',
            'section' => 'available',
            'label' => Mage::helper('flexitheme')->__('Available Blocks'),
            'group' => $field_prefix_name . '_columns',
            'static' => true,
            'values' => Mage::getModel('flexitheme/layout_block')->toOptionArray(),
        ));

        $fieldset->addField($field_prefix_name . '_layout_xml_update', 'textarea', array(
            'name' => 'layout_xml_update',
            'label' => Mage::helper('flexitheme')->__('Custom Layout Xml'),
            'note' => 'Do not edit unless you know what you are doing. PLEASE !!'
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
