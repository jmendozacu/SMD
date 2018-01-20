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

class Blackbird_ContentManager_Block_Adminhtml_ContentType_Edit_Tab_Layout extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $contentTypeId = $this->getRequest()->getParam('ct_id');
        $entity_id = $this->getRequest()->getParam('id');
        
        $fieldset = $form->addFieldset('contenttype_layout', array('legend'=>Mage::helper('contentmanager')->__('Step 1 - Select your layout')));            
        
        $fieldset->addField('root_template', 'select', array(
            'name'     => 'root_template',
            'label'    => Mage::helper('cms')->__('Layout general'),
            'required' => true,
            'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
            'note'      => Mage::helper('contentmanager')->__('Modify general layout'),
        ));

        $fieldset->addField('layout_update_xml', 'textarea', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('cms')->__('Layout Update XML'),
            'style'     => 'height:24em;',
        ));        
        
        $fieldset->addField('layout', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Content layout'),
            'name'      => 'layout',
            'note'      => Mage::helper('contentmanager')->__('Select to preview the content layout'),
        ));
        //new renderer
        $form->getElement('layout')->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_layout'));

        
        $fieldset2 = $form->addFieldset('contenttype_layout_items', array('legend'=>Mage::helper('contentmanager')->__('Step 2 - Drag and drop items in your layout')));
        $fieldset2->addField('layout_items', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Layout items'),
            'name'      => 'layout_items',
        ));
        //new renderer
        $form->getElement('layout_items')->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_layout_items'));
        
        
        $fieldset3 = $form->addFieldset('contenttype_layout_grid', array('legend'=>Mage::helper('contentmanager')->__('Step 3 - Configure your items')));
        $fieldset3->addField('layout_configure', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Layout configure'),
            'name'      => 'layout_configure',
        ));
        //new renderer
        $form->getElement('layout_configure')->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_layout_configure'));
        
        
        if ( Mage::getSingleton('adminhtml/session')->getContentTypeData() )
        {
            Mage::getSingleton('adminhtml/session')->setContentTypeData(null);
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentTypeData());    
            
        } elseif ( Mage::registry('contenttype_data') ) {
            
            if (!Mage::registry('contenttype_data')->getRootTemplate()) {
                Mage::registry('contenttype_data')->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
            }
            $form->setValues(Mage::registry('contenttype_data')->getData());
            
        }
        
        return parent::_prepareForm();
    }
}