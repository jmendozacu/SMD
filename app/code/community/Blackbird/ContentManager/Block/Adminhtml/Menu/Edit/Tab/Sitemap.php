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

class Blackbird_ContentManager_Block_Adminhtml_Menu_Edit_Tab_Sitemap extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('menu_sitemap', array('legend'=>Mage::helper('contentmanager')->__('Sitemap')));            
        
        $fieldset->addField('sitemap_enable', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Enabled'),
            'name'      => 'sitemap_enable',
            'values'    => array(
                array('value'=>'1', 'label'=>Mage::helper('contentmanager')->__('Yes')),
                array('value'=>'0', 'label'=>Mage::helper('contentmanager')->__('No')),
            ),
            'note'      => Mage::helper('contentmanager')->__('Make sure the Google Sitemap is activated. (System > Configuration > Catalog > Google Sitemap)'),
        ));
        
        $fieldset->addField('sitemap_frequency', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Frequency (level 0)'),
            'required'  => true,
            'name'      => 'sitemap_frequency',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority (level 0)'),
            'name'      => 'sitemap_priority',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
        ));
        $fieldset->addField('sitemap_frequency_level1', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Frequency (level 1)'),
            'required'  => true,
            'name'      => 'sitemap_frequency_level1',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority_level1', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority (level 1)'),
            'name'      => 'sitemap_priority_level1',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
        ));
        $fieldset->addField('sitemap_frequency_level2', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Frequency (level 2)'),
            'required'  => true,
            'name'      => 'sitemap_frequency_level2',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority_level2', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority (level 2)'),
            'name'      => 'sitemap_priority_level2',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
        ));
        $fieldset->addField('sitemap_frequency_level3', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Frequency (level 3)'),
            'required'  => true,
            'name'      => 'sitemap_frequency_level3',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority_level3', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority (level 3)'),
            'name'      => 'sitemap_priority_level3',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
        ));
        $fieldset->addField('sitemap_frequency_level4', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Frequency (level 4)'),
            'required'  => true,
            'name'      => 'sitemap_frequency_level4',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority_level4', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority (level 4)'),
            'name'      => 'sitemap_priority_level4',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
        ));
        
        
        

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