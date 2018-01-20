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

class Blackbird_ContentManager_Block_Adminhtml_Contentlist_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
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
        
        $contentTypeId = $this->getRequest()->getParam('cl_id');
        $entity_id = $this->getRequest()->getParam('id');
        
        $fieldset2 = $form->addFieldset('contenttype_meta', array('legend'=>Mage::helper('contentmanager')->__('Meta tags')));            
        
         $fieldset2->addField('meta_title', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Page Title'),
            'name'      => 'meta_title',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        )); 
         
        $fieldset2->addField('meta_description', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Description'),
            'name'      => 'meta_description',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset2->addField('meta_keywords', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Keywords'),
            'name'      => 'meta_keywords',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $values[] = array(
            'value'     => 'INDEX,FOLLOW',
            'label'     => 'INDEX,FOLLOW'
        );
        $values[] = array(
            'value'     => 'NOINDEX,FOLLOW',
            'label'     => 'NOINDEX,FOLLOW'
        );
        $values[] = array(
            'value'     => 'INDEX,NOFOLLOW',
            'label'     => 'INDEX,NOFOLLOW'
        );
        $values[] = array(
            'value'     => 'NOINDEX,NOFOLLOW',
            'label'     => 'NOINDEX,NOFOLLOW'
        );
        
        $fieldset2->addField('meta_robots', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Robots'),
            'required'  => true,
            'name'      => 'meta_robots',
            'values' => $values,
        ));
        
        $fieldset3 = $form->addFieldset('contenttype_og', array('legend'=>Mage::helper('contentmanager')->__('Open Graph')));            
        
        $fieldset3->addField('og_title', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'name'      => 'og_title',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset3->addField('og_description', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Description'),
            'name'      => 'og_description',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset3->addField('og_url', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'name'      => 'og_url',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset3->addField('og_type', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Type'),
            'name'      => 'og_type',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $fieldset3->addField('og_image', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Image'),
            'name'      => 'og_image',
            'note'      => Mage::helper('contentmanager')->__('You can use replacement pattern.<br/>Example: <strong>{{title}}</strong> will be automatically replaced by the field value of the content (field with the identifier "title").<br/>Use plain text value of a field, type <strong>{{title|plain}}</strong>'),
        ));
        
        $model = Mage::registry('contentlist_data');
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