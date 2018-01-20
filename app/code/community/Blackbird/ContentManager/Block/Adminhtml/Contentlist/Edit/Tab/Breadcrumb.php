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

class Blackbird_ContentManager_Block_Adminhtml_Contentlist_Edit_Tab_Breadcrumb extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('contentlist_breadcrumb', array('legend'=>Mage::helper('contentmanager')->__('Breadcrumbs')));
       
        $fieldset->addField('breadcrumb', 'select', array(
            'name'      => 'breadcrumb',
            'label'     => Mage::helper('contentmanager')->__('Last crumb'),
            'title'     => Mage::helper('contentmanager')->__('Last crumb'),
            'note'      => Mage::helper('contentmanager')->__('Select the field to use as breadcrumb. You can create a new field dedicated to the breadcrumb name. Save your content in order to see your new fields in this list.'),
            'required'  => false,
            'options'   => 
                array(
                    '' => Mage::helper('contentmanager')->__('No breadcrumb'),
                    'title' => Mage::helper('contentmanager')->__('Page Title'),
                    'breadcrumb_custom_title' => Mage::helper('contentmanager')->__('Custom breadcrumb'),
                    )
            ,
        ));
        
        $fieldset->addField('breadcrumb_custom_title', 'text', array(
            'name'      => 'breadcrumb_custom_title',
            'label'     => Mage::helper('contentmanager')->__('Custom Breadcrumb'),
            'title'     => Mage::helper('contentmanager')->__('Custom Breadcrumb'),
            'note'      => Mage::helper('contentmanager')->__('Working if you select "custom breadcrumb" above'),
            'required'  => false,
            'options'   => 
                array(
                    '' => 'No breadcrumb',
                    'title' => 'Page Title',
                    'breadcrumb_custom_title' => 'Custom breadcrumb',
                    )
            ,
        ));
        
        $stores = Mage::app()->getStores();
        foreach($stores as $store)
        {
            $fieldsetStore[$store->getId()] = $form->addFieldset('contentlist_form'.$store->getId(), array('legend'=>Mage::helper('contentmanager')->__('Middle breadcrumb - '.$store->getName().' ('.$store->getCode().')')));
            $fieldsetStore[$store->getId()]->addField('breadcrumb_prev_name_'.$store->getId(), 'text', array(
                'name'      => 'breadcrumb_prev_name['.$store->getId().']',
                'label'     => Mage::helper('contentmanager')->__('N-1 bread crumb name'),
                'title'     => Mage::helper('contentmanager')->__('N-1 bread crumb name'),
                'note'      => Mage::helper('contentmanager')->__('You can add a middle crumb, keep empty to skip this feature.'),
                'required'  => false,
            ));

            $fieldsetStore[$store->getId()]->addField('breadcrumb_prev_link_'.$store->getId(), 'text', array(
                'name'      => 'breadcrumb_prev_link['.$store->getId().']',
                'label'     => Mage::helper('contentmanager')->__('N-1 bread crumb link'),
                'title'     => Mage::helper('contentmanager')->__('N-1 bread crumb link'),
                'note'      => Mage::helper('contentmanager')->__('Type your middle crumb link, keep empty for no link on it.'),
                'required'  => false,
            ));            
        }
       
        $model = Mage::registry('contentlist_data');
        if ( Mage::getSingleton('adminhtml/session')->getContentListData() )
        {
            $model = Mage::registry('contentlist_data');
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentListData());
            Mage::getSingleton('adminhtml/session')->setContentListData(null);
        } elseif ( Mage::registry('contentlist_data') ) {
            $data = Mage::registry('contentlist_data')->getData();
            
            if(isset($data['breadcrumb_prev_name']))
                $data['breadcrumb_prev_name'] = unserialize($data['breadcrumb_prev_name']);
            
            if(isset($data['breadcrumb_prev_link']))
                $data['breadcrumb_prev_link'] = unserialize($data['breadcrumb_prev_link']);
            
            $stores = Mage::app()->getStores();
            foreach($stores as $store)
            {
                if(isset($data['breadcrumb_prev_name']) && isset($data['breadcrumb_prev_name'][$store->getId()]))
                    $data['breadcrumb_prev_name_'.$store->getId()] = $data['breadcrumb_prev_name'][$store->getId()];
                
                if(isset($data['breadcrumb_prev_link']) && isset($data['breadcrumb_prev_link'][$store->getId()]))
                    $data['breadcrumb_prev_link_'.$store->getId()] = $data['breadcrumb_prev_link'][$store->getId()];
            }
            
            $form->setValues($data);
        }
        return parent::_prepareForm();
    }
}