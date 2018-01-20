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

class Blackbird_ContentManager_Block_Adminhtml_Menu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
/**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('menu_form');
        $this->setTitle(Mage::helper('contentmanager')->__('Menu Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $model = Mage::registry('menu_data');
        
        $fieldset = $form->addFieldset('menu_form', array('legend'=>Mage::helper('contentmanager')->__('General')));
        
        if ($model->getMenuId()) {
            $fieldset->addField('menu_id', 'hidden', array(
                'name' => 'menu_id',
            ));
        }
       
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        
        $fieldset->addField('identifier', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Identifier'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'identifier',
        ));
        
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('contentmanager')->__('Status'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'status',
            'values'    => array(
                '1' => Mage::helper('contentmanager')->__('Enabled'),
                '0' => Mage::helper('contentmanager')->__('Disabled'),
            ),
        ));
        
        /**
         * Check is single store mode
         */
        if(Mage::helper('contentmanager')->isMenuAllowed(0))
        {
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
        }
        
        /**
         * Tree menu fieldset
         */
        $treeFieldset = $form->addFieldset('menu_form_tree', array('legend'=>Mage::helper('contentmanager')->__('Tree menu')));
        
        $treeField = $treeFieldset->addField('nodes', 'text', array(
            'label'     => Mage::helper('contentmanager')->__('Tree menu'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'nodes',
        ));
        $renderer = $this->getLayout()->createBlock('contentmanager/adminhtml_menu_edit_renderer_tree');
        $treeField->setRenderer($renderer);
        
        /***********************************************************/

        if ( Mage::getSingleton('adminhtml/session')->getMenuData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMenuData());
            Mage::getSingleton('adminhtml/session')->setMenuData(null);
        } elseif ( Mage::registry('menu_data') ) {
            $form->setValues(Mage::registry('menu_data')->getData());
        }
        return parent::_prepareForm();
    }
}