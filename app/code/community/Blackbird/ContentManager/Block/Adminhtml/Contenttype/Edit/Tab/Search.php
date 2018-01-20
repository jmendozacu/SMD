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

class Blackbird_ContentManager_Block_Adminhtml_ContentType_Edit_Tab_Search extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $contentTypeId = $this->getRequest()->getParam('ct_id');
        $entity_id = $this->getRequest()->getParam('id');
        
        $fieldset = $form->addFieldset('contenttype_review', array('legend'=>Mage::helper('contentmanager')->__('Search')));            

        $fieldset->addField('search_enabled', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Enabled'),
            'name'      => 'search_enabled',
            'options'   => array(
                0 => Mage::helper('adminhtml')->__('No'),
                1 => Mage::helper('adminhtml')->__('Yes'),
            ),
            'note'      => Mage::helper('contentmanager')->__('Set to yes if you want your content type searchable in Frontend default Magento search functionality.<br/>After enabling it, SAVE your Content Type and then define which fields are searchable.'),
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