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

class Blackbird_ContentManager_Block_Adminhtml_ContentType_Edit_Tab_Url extends Mage_Adminhtml_Block_Widget_Form
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
        
        $fieldset = $form->addFieldset('contenttype_url', array('legend'=>Mage::helper('contentmanager')->__('Default URL pattern')));            

        $fieldset->addField('default_url', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'name'      => 'default_url',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset2 = $form->addFieldset('contenttype_menu', array('legend'=>Mage::helper('contentmanager')->__('Menu settings')));            

        $fieldset2->addField('url_menu', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Allow URLs based on menu hierarchy'),
            'name'      => 'url_menu',
            'options'   => array(
                0 => Mage::helper('contentmanager')->__('No'),
                1 => Mage::helper('contentmanager')->__('Yes'),
            ),
            'note'      => Mage::helper('contentmanager')->__('You can define a new url each time you link this content to a menu node.'),
        ));

        if ( Mage::getSingleton('adminhtml/session')->getContentTypeData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentTypeData());
            Mage::getSingleton('adminhtml/session')->setContentTypeData(null);
        } elseif ( Mage::registry('contenttype_data') ) {
            $data = Mage::registry('contenttype_data')->getData();
            if(!isset($data['default_url']) || !$data['default_url'])
            {
                $data['default_url'] = '{{title|plain}}';                
            }
            
            $form->setValues($data);
        }
        
        return parent::_prepareForm();
    }
}