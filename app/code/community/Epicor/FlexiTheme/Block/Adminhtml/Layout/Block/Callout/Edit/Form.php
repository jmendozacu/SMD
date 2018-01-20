<?php
 
class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Callout_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));
        $model = Mage::registry('layout_block_data');
        $data = unserialize($model->getBlockExtra());
        $data['entity_id'] = $model->getId();

        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('adminhtml')->__('Callout block information')));
        
        $fieldset->addType('image_replace', 'Epicor_FlexiTheme_Lib_Varien_Data_Form_Element_Image');
       
        $fieldset->addField('entity_id', 'hidden', array(
            'name' => 'block_id',
        ));
        
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
        
        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Type'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'type',
            'values' => Mage::getModel('flexitheme/config_source_callouttypes')->toOptionArray(),
        ));
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Block Title'),
            'name' => 'title',
        ));
        
        $fieldset->addField('image', 'image_replace', array(
              'label'       => Mage::helper('adminhtml')->__('Image'),
              'name'        => 'image',
              'image'       => @$data['image'] ,
              'name_suffix' => $form->getFieldNameSuffix(),
        ));
        
        $fieldset->addField('url', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Url'),
            'name' => 'url',
        ));
        
        $fieldset->addField('image_alt', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Image Alt Text'),
            'name' => 'image_alt',
        ));
        
        $fieldset->addField('product_sku', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Product SKU'),
            'name' => 'product_sku',
        ));
        
        // set up config for editor, so that urls work
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(); 
        $wysiwygConfig->addData(
            array(
                'variables_wysiwyg_action_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin'), 
                'widget_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'), 
                'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'), 
                'directives_url_quoted' => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')), 
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            )
        );
        
        $fieldset->addField('html', 'editor', array(
            'label' => Mage::helper('adminhtml')->__('Custom HTML'),
            'name' => 'html',    
            'config'    => $wysiwygConfig,
            'wysiwyg'   => true,
            'style'     => 'height:12em;width:500px;',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}