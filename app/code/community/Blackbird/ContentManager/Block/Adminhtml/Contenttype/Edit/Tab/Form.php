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

class Blackbird_ContentManager_Block_Adminhtml_Contenttype_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('contenttype_form', array('legend'=>Mage::helper('contentmanager')->__('Informations')));
       
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'class'     => 'required-entry',
            'onkeyup'   => 'checkCtTitle(this);',
            'required'  => true,
            'name'      => 'title',
        ));
        
        $fieldset->addField('identifier', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Identifier'),
            'class'     => 'required-entry ct-identifier',
            'onkeyup'   => 'checkCtIdentifier(this);',
            'required'  => true,
            'name'      => 'identifier',
        ));      
        
        $fieldset->addField('default_status', 'select', array(
            'label'     => Mage::helper('contentmanager')->__('Default status'),
            'options'   => array(
                0 => Mage::helper('contentmanager')->__('Disabled'),
                1 => Mage::helper('contentmanager')->__('Enabled'),
            ),
            'name'      => 'default_status',
        ));  
        
        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('contentmanager')->__('Description'),
            'title'     => Mage::helper('contentmanager')->__('Description'),
            'required'  => false,
        )); 

        if ( Mage::getSingleton('adminhtml/session')->getContentTypeData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentTypeData());
            Mage::getSingleton('adminhtml/session')->setContentTypeData(null);
        } elseif ( Mage::registry('contenttype_data') ) {
            $form->setValues(Mage::registry('contenttype_data')->getData());
        }
        return parent::_prepareForm();
    }
}