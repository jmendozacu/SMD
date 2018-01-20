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

class Blackbird_ContentManager_Block_Adminhtml_ContentType_Edit_Tab_Sitemap extends Mage_Adminhtml_Block_Widget_Form
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
        
        $contentTypeId = $this->getRequest()->getParam('ct_id');
        $entity_id = $this->getRequest()->getParam('id');
        
        $fieldset = $form->addFieldset('contenttype_sitemap', array('legend'=>Mage::helper('contentmanager')->__('Sitemap')));            
        
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
            'label'     => Mage::helper('contentmanager')->__('Frequency'),
            'required'  => true,
            'name'      => 'sitemap_frequency',
            'values' => Mage::getSingleton('adminhtml/system_config_source_frequency')->toOptionArray(),
        ));
        
        $fieldset->addField('sitemap_priority', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Priority'),
            'name'      => 'sitemap_priority',
            'note'      => Mage::helper('contentmanager')->__('Valid values range: from 0.0 to 1.0.'),
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