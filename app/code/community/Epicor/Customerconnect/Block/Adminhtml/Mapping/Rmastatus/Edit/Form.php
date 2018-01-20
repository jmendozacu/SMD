<?php

class Epicor_Customerconnect_Block_Adminhtml_Mapping_Rmastatus_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{

    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getRmastatusMappingData()) {
            $data = Mage::getSingleton('adminhtml/session')->getRmastatusMappingData();
            Mage::getSingleton('adminhtml/session')->getRmastatusMappingData(null);
        } elseif (Mage::registry('rmastatus_mapping_data')) {
            $data = Mage::registry('rmastatus_mapping_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form(
            array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'mapping_form',
            array(
            'legend' => Mage::helper('adminhtml')->__('Mapping Information')
            )
        );

        $fieldset->addField(
            'code', 'text',
            array(
            'label' => Mage::helper('adminhtml')->__('Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code',
            'note' => Mage::helper('adminhtml')->__('Rma Status Code'),
            )
        );


        $fieldset->addField(
            'status', 'text',
            array(
            'name' => 'status',
            'label' => Mage::helper('sales')->__('Rma Status'),
            'class' => 'required-entry',
            'values' => 'status',
            'required' => true,
            )
        );

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

        $fieldset->addField(
            'status_text', 'editor',
            array(
            'label' => Mage::helper('adminhtml')->__('Status Text Displayed to Customers'),
            'name' => 'status_text',
            'config' => $wysiwygConfig,
            'wysiwyg' => true,
            'style' => 'height:12em;width:500px;',
            )
        );

        $fieldset->addField(
            'is_rma_deleted', 'checkbox',
            array(
            'name' => 'is_rma_deleted',
            'label' => Mage::helper('sales')->__('Delete RMAs Changed to this Status?'),
                'checked' => $data['is_rma_deleted'] ? 'checked' : ''
            )
        );


//
//        $fieldset->addField('state', 'text',
//            array(
//                'name'      => 'state',
//                'label'     => Mage::helper('sales')->__('Rma State'),
//                'class'     => 'required-entry',
//                'values'    => 'state',
//                'required'  => true,
//            )
//        );

        $data = $this->includeStoreIdElement($data);

        $form->setValues($data);

        return parent::_prepareForm();
    }

}
