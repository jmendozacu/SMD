<?php
 
class Epicor_Comm_Block_Adminhtml_Mapping_Remotelinks_Edit_Form extends Epicor_Common_Block_Adminhtml_Mapping_Default_Form
{
   
    protected function _prepareForm()
    {
        if (Mage::getSingleton('adminhtml/session')->getRemotelinksMappingData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getRemotelinksMappingData();
            Mage::getSingleton('adminhtml/session')->getRemotelinksMappingData(null);
        }
        elseif (Mage::registry('remotelinks_mapping_data'))
        {
            $data = Mage::registry('remotelinks_mapping_data')->getData();
            if(array_key_exists('name', $data)){
                Mage::getSingleton('adminhtml/session')->setRemoteLink($data['name']);
            }else{
                Mage::getSingleton('adminhtml/session')->unsRemoteLink();                
            }        
        }
        else
        {
            $data = array();
        }

        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('mapping_form', array(
             'legend' =>Mage::helper('adminhtml')->__('Mapping Information')
        ));
 
        $fieldset->addField('pattern_code', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Pattern Code'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'pattern_code',
        ));
 
        $fieldset->addField('name', 'select', array(
             'label'     => Mage::helper('adminhtml')->__('Name'),
             'options'   => Mage::getSingleton('epicor_comm/config_source_remotelinkobjects')->toOptionArray(),       
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'name',
        ));
        
        // set up config for editor, so that urls work
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        
        //as setting variables_wysiwyg_action_url doesn't work, have to update plugins element
        $plugins = $wysiwygConfig->getData('plugins');
        $plugins[0]['options']['url'] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/epicorcomm_system_variable/wysiwygPlugin');
         $plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/epicorcomm_system_variable/wysiwygPlugin')."', '{{html_id}}');";
        $wysiwygConfig->setData('plugins', $plugins); 
        $wysiwygConfig->setAddImages(false);
        $wysiwygConfig->setAddWidgets(false);
        
        $fieldset->addField('url_pattern', 'editor', array(
            'label' => Mage::helper('adminhtml')->__('Url Pattern'),
            'name' => 'url_pattern', 
            'required'  => true,
            'config'    => $wysiwygConfig,
            'wysiwyg'   => false,
            'style'     => 'height:4em;width:500px;',
        ));
        $fieldset->addField('auth_user', 'text', array(
             'label'     => Mage::helper('adminhtml')->__('Authorised User'),
             'required'  => false,
             'name'      => 'auth_user',
        ));
        $fieldset->addField('auth_password', 'password', array(
             'label'     => Mage::helper('adminhtml')->__('Authorised Password'),
             'required'  => false,
             'name'      => 'auth_password',
        ));

        $data = $this->includeStoreIdElement($data);
 
        $form->setValues($data);

        return parent::_prepareForm();
       
    }
}