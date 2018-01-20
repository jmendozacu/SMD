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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://www.blackbird.fr)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Content_Edit_Tab_Url extends Mage_Adminhtml_Block_Widget_Form
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
        $contentType = Mage::getModel('contentmanager/contenttype')->load($contentTypeId);
        
        $fieldset = $form->addFieldset('contenttype_url', array('legend'=>Mage::helper('contentmanager')->__('URL informations')));            

        $fieldset->addField('url_key', 'text' , array(
            'label'                 => Mage::helper('contentmanager')->__('URL'),
            'required'              => true,
            'name'                  => 'url_key',
            'after_element_html'    => '<p><input id="regenerate_url" name="regenerate_url" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'url_key\').value=\''.$contentType->getDefaultUrl().'\'; $(\'url_key\').writeAttribute(\'readonly\', \'readonly\'); $(\'url_key\').addClassName(\'disabled\'); }  else { $(\'url_key\').value=\''.Mage::registry('content_data')->getData('url_key').'\';  $(\'url_key\').removeAttribute(\'readonly\'); $(\'url_key\').removeClassName(\'disabled\'); }"> <label for="regenerate_url" class="inherit">'.Mage::helper('contentmanager')->__('Regenerate URL').'</label></p>'
        ));
        
        if($contentType->getUrlMenu() && $entity_id)
        {
            /**
             * Tree menu url
             */
            $treeFieldset = $form->addFieldset('contenttype_url_menu', array('legend'=>Mage::helper('contentmanager')->__('Menu node URL')));

            $treeField = $treeFieldset->addField('nodes', 'text', array(
                'label'     => Mage::helper('contentmanager')->__('Menu node URL'),
                'name'      => 'nodes',
            ));
            $renderer = $this->getLayout()->createBlock('contentmanager/adminhtml_content_edit_renderer_menu');
            $renderer->setContentId($entity_id);
            $treeField->setRenderer($renderer);
        }
        

        if ( Mage::getSingleton('adminhtml/session')->getContentData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentData());
            Mage::getSingleton('adminhtml/session')->setContentData(null);
        }
        elseif ( Mage::registry('content_data') )
        {
            //not existing contenttype id -> add default value
            if(!$entity_id)
            {
                $form->setValues(array('url_key' => $contentType->getDefaultUrl()));
            }
            else
            {
                Mage::registry('content_data')->setCtId($contentTypeId);
                $form->setValues(Mage::registry('content_data')->getData());                
            }
        }
        return parent::_prepareForm();
    }
}