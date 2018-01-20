<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Contentlist_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('contentlist_form', array('legend'=>Mage::helper('contentmanager')->__('Informations')));
      
        $model = Mage::registry('contentlist_data');
        if ($model->getClId()) {
            $fieldset->addField('cl_id', 'hidden', array(
                'name' => 'cl_id',
            ));
        }
        
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'class'     => 'required-entry',
            'onkeyup'   => 'checkCtTitle(this);',
            'required'  => true,
            'name'      => 'title',
        ));
        $fieldset->addField('url_key', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'url_key',
        ));      
        
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('contentmanager')->__('Default status'),
            'options'   => array(
                0 => Mage::helper('contentmanager')->__('Disabled'),
                1 => Mage::helper('contentmanager')->__('Enabled'),
            ),
            'name'      => 'status',
        ));  
        /**
         * Check is single store mode
         */
        
            if (!Mage::app()->isSingleStoreMode()) {
                $field =$fieldset->addField('store_id', 'multiselect', array(
                    'name'      => 'stores[]',
                    'label'     => Mage::helper('cms')->__('Store View'),
                    'title'     => Mage::helper('cms')->__('Store View'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                ));
                $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
                $field->setRenderer($renderer);
            }
            else {
                $fieldset->addField('store_id', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                ));
                $model->setStoreId(Mage::app()->getStore(true)->getId());
            }
            
        $fieldset->addField('text_before', 'editor', array(
            'label'     => Mage::helper('contentmanager')->__('Text Before'),
            'title'     => Mage::helper('contentmanager')->__('Text Before'),
            'required'  => false,
            'style'     => 'height:26em;width:46em;',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
            'name'      => 'text_before',
        )); 
        $fieldset->addField('text_after', 'editor', array(
            'label'     => Mage::helper('contentmanager')->__('Text After'),
            'title'     => Mage::helper('contentmanager')->__('Text After'),
            'required'  => false,
            'style'     => 'height:26em;width:46em;',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
            'name'      => 'text_after',
        )); 

        if ( Mage::getSingleton('adminhtml/session')->getContentListData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentListData());
            Mage::getSingleton('adminhtml/session')->setContentListData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }
        return parent::_prepareForm();
    }
}