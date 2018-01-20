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

class Blackbird_ContentManager_Block_Adminhtml_Content_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
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
        $content = Mage::registry('content_data');
        
        
        $fieldset2 = $form->addFieldset('contenttype_meta', array('legend'=>Mage::helper('contentmanager')->__('Meta tags')));            
        
        $fieldset2->addField('meta_title', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'name'      => 'meta_title',
            (($content->getUseDefaultMetaTitle() === '1' || !$entity_id)?'readonly':'') => (($content->getUseDefaultMetaTitle() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultMetaTitle() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultMetaTitle() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_meta_title" name="use_default_meta_title" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'meta_title\').value=\''.addslashes($contentType->getMetaTitle()).'\'; $(\'meta_title\').writeAttribute(\'readonly\', \'readonly\'); $(\'meta_title\').addClassName(\'disabled\'); }  else { $(\'meta_title\').value=\''.addslashes($content->getData('meta_title')).'\';  $(\'meta_title\').removeAttribute(\'readonly\'); $(\'meta_title\').removeClassName(\'disabled\'); }"> <label for="use_default_meta_title" class="inherit">'.Mage::helper('contentmanager')->__('Use default meta_title').'</label></p>'
        ));
        
        $fieldset2->addField('description', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Description'),
            'name'      => 'description',
            (($content->getUseDefaultDescription() === '1' || !$entity_id)?'readonly':'') => (($content->getUseDefaultDescription() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultDescription() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultDescription() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_description" name="use_default_description" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'description\').value=\''.addslashes($contentType->getMetaDescription()).'\'; $(\'description\').writeAttribute(\'readonly\', \'readonly\'); $(\'description\').addClassName(\'disabled\'); }  else { $(\'description\').value=\''.addslashes($content->getData('description')).'\';  $(\'description\').removeAttribute(\'readonly\'); $(\'description\').removeClassName(\'disabled\'); }"> <label for="use_default_description" class="inherit">'.Mage::helper('contentmanager')->__('Use default description').'</label></p>'
        ));
        
        $fieldset2->addField('keywords', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Keywords'),
            'name'      => 'keywords',
            (($content->getUseDefaultKeywords() === '1' || !$entity_id)?'readonly':'') => (($content->getUseDefaultKeywords() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultKeywords() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultKeywords() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_keywords" name="use_default_keywords" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'keywords\').value=\''.addslashes($contentType->getMetaKeywords()).'\'; $(\'keywords\').writeAttribute(\'readonly\', \'readonly\'); $(\'keywords\').addClassName(\'disabled\'); }  else { $(\'keywords\').value=\''.addslashes($content->getData('keywords')).'\';  $(\'keywords\').removeAttribute(\'readonly\'); $(\'keywords\').removeClassName(\'disabled\'); }"> <label for="use_default_keywords" class="inherit">'.Mage::helper('contentmanager')->__('Use default keywords').'</label></p>'
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
        
        $fieldset2->addField('robots', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Robots'),
            'required'  => true,
            'name'      => 'robots',
            'values' => $values
        ));
        
        $fieldset3 = $form->addFieldset('contenttype_og', array('legend'=>Mage::helper('contentmanager')->__('Open Graph')));            
        
        $fieldset3->addField('og_title', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'name'      => 'og_title',
            (($content->getUseDefaultOgTitle() === '1' || !$entity_id)?'readonly':'') => (($content->getUseDefaultOgTitle() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultOgTitle() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultOgTitle() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_og_title" name="use_default_og_title" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'og_title\').value=\''.addslashes($contentType->getOgTitle()).'\'; $(\'og_title\').writeAttribute(\'readonly\', \'readonly\'); $(\'og_title\').addClassName(\'disabled\'); }  else { $(\'og_title\').value=\''.addslashes($content->getData('og_title')).'\';  $(\'og_title\').removeAttribute(\'readonly\'); $(\'og_title\').removeClassName(\'disabled\'); }"> <label for="use_default_og_title" class="inherit">'.Mage::helper('contentmanager')->__('Use default OG title').'</label></p>'
        ));
        
        $fieldset3->addField('og_description', 'textarea' , array(
            'label'     => Mage::helper('contentmanager')->__('Description'),
            'name'      => 'og_description',
            (($content->getUseDefaultOgDescription() === '1' || !$entity_id)?'readonly':'')  => (($content->getUseDefaultOgDescription() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultOgDescription() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultOgDescription() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_og_description" name="use_default_og_description" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'og_description\').value=\''.addslashes($contentType->getOgDescription()).'\'; $(\'og_description\').writeAttribute(\'readonly\', \'readonly\'); $(\'og_description\').addClassName(\'disabled\'); }  else { $(\'og_description\').value=\''.addslashes($content->getData('og_description')).'\';  $(\'og_description\').removeAttribute(\'readonly\'); $(\'og_description\').removeClassName(\'disabled\'); }"> <label for="use_default_og_description" class="inherit">'.Mage::helper('contentmanager')->__('Use default OG description').'</label></p>'
        ));
        
        $fieldset3->addField('og_url', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('URL'),
            'name'      => 'og_url',
            (($content->getUseDefaultOgUrl() === '1' || !$entity_id)?'readonly':'')  => (($content->getUseDefaultOgUrl() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultOgUrl() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultOgUrl() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_og_url" name="use_default_og_url" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'og_url\').value=\''.addslashes($contentType->getOgUrl()).'\'; $(\'og_url\').writeAttribute(\'readonly\', \'readonly\'); $(\'og_url\').addClassName(\'disabled\'); }  else { $(\'og_url\').value=\''.addslashes($content->getData('og_url')).'\';  $(\'og_url\').removeAttribute(\'readonly\'); $(\'og_url\').removeClassName(\'disabled\'); }"> <label for="use_default_og_url" class="inherit">'.Mage::helper('contentmanager')->__('Use default OG url').'</label></p>'
        ));
        
        $fieldset3->addField('og_type', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Type'),
            'name'      => 'og_type',
            (($content->getUseDefaultOgType() === '1' || !$entity_id)?'readonly':'')  => (($content->getUseDefaultOgType() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultOgType() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultOgType() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_og_type" name="use_default_og_type" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'og_type\').value=\''.addslashes($contentType->getOgType()).'\'; $(\'og_type\').writeAttribute(\'readonly\', \'readonly\'); $(\'og_type\').addClassName(\'disabled\'); }  else { $(\'og_type\').value=\''.addslashes($content->getData('og_type')).'\';  $(\'og_type\').removeAttribute(\'readonly\'); $(\'og_type\').removeClassName(\'disabled\'); }"> <label for="use_default_og_type" class="inherit">'.Mage::helper('contentmanager')->__('Use default OG type').'</label></p>'
        ));
        
        $fieldset3->addField('og_image', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Image'),
            'name'      => 'og_image',
            (($content->getUseDefaultOgImage() === '1' || !$entity_id)?'readonly':'')  => (($content->getUseDefaultOgImage() === '1' || !$entity_id)?'readonly':''),
            'class'     => (($content->getUseDefaultOgImage() === '1' || !$entity_id)?'disabled':''),
            'after_element_html'    => '<p><input '.(($content->getUseDefaultOgImage() === '1' || !$entity_id)?'checked="checked"':'').' id="use_default_og_image" name="use_default_og_image" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'og_image\').value=\''.addslashes($contentType->getOgImage()).'\'; $(\'og_image\').writeAttribute(\'readonly\', \'readonly\'); $(\'og_image\').addClassName(\'disabled\'); }  else { $(\'og_image\').value=\''.addslashes($content->getData('og_image')).'\';  $(\'og_image\').removeAttribute(\'readonly\'); $(\'og_image\').removeClassName(\'disabled\'); }"> <label for="use_default_og_image" class="inherit">'.Mage::helper('contentmanager')->__('Use default OG image').'</label></p>'
        ));
        

        if ( Mage::getSingleton('adminhtml/session')->getContentData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentData());
            Mage::getSingleton('adminhtml/session')->setContentData(null);
        }
        elseif ( $content )
        {
            //not existing contenttype id -> add default value
            if(!$entity_id)
            {
                $form->setValues(array(
                    'meta_title' => $contentType->getMetaTitle(),
                    'description' => $contentType->getMetaDescription(),
                    'keywords' => $contentType->getMetaKeywords(),
                    'robots' => $contentType->getMetaRobots(),
                    'og_title' => $contentType->getOgTitle(),
                    'og_url' => $contentType->getOgUrl(),
                    'og_description' => $contentType->getOgDescription(),
                    'og_image' => $contentType->getOgImage(),
                    'og_type' => $contentType->getOgType(),
                ));
            }
            else
            {
                $content->setCtId($contentTypeId);
                $values = $content->getData();
                $values = array_merge($values, array(
                    'meta_title' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getMetaTitle():$content->getMetaTitle()),
                    'description' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getMetaDescription():$content->getDescription()),
                    'keywords' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getMetaKeywords():$content->getKeywords()),
                    'og_title' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getOgTitle():$content->getOgTitle()),
                    'og_url' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getOgUrl():$content->getOgUrl()),
                    'og_description' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getOgDescription():$content->getOgDescription()),
                    'og_image' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getOgImage():$content->getOgImage()),
                    'og_type' => (($content->getUseDefaultTitle() === '1' || !$entity_id)?$contentType->getOgType():$content->getOgType()),
                ));
                $form->setValues($values);
            }
        }
        
        return parent::_prepareForm();
    }
}