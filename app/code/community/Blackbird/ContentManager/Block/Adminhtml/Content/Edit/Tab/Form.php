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

class Blackbird_ContentManager_Block_Adminhtml_Content_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
        $helper = Mage::helper('contentmanager');
        
        $contentTypeId = $this->getRequest()->getParam('ct_id');
        $entity_id = $this->getRequest()->getParam('id');
        $contentType = Mage::getModel('contentmanager/contenttype')->load($contentTypeId);
        $content = Mage::registry('content_data');
        
        $hiddenCctIdAdded = false;
        $default_values = array();
        
        //default fieldset
        $fieldset = $form->addFieldset('contenttype_title', array('legend'=>Mage::helper('contentmanager')->__('General')));            

        $fieldset->addField('title', 'text' , array(
            'label'     => Mage::helper('contentmanager')->__('Title'),
            'required'  => true,
            ((($content->getUseDefaultTitle() === '1' || !$entity_id) && $contentType->getPageTitle())?'readonly':'')  => ((($content->getUseDefaultTitle() === '1' || !$entity_id) && $contentType->getPageTitle())?'readonly':''),
            'class'     => ((($content->getUseDefaultTitle() === '1' || !$entity_id) && $contentType->getPageTitle())?'disabled':''),
            'name'      => 'title',
            'after_element_html'    => '<p><input '.((($content->getUseDefaultTitle() === '1' || !$entity_id) && $contentType->getPageTitle())?'checked="checked"':'').' id="use_default_title" name="use_default_title" type="checkbox" value="1" class="checkbox" onclick="if(this.checked) { $(\'title\').value=\''.addslashes($contentType->getPageTitle()).'\'; $(\'title\').writeAttribute(\'readonly\', \'readonly\'); $(\'title\').addClassName(\'disabled\'); }  else { $(\'title\').value=\''.addslashes($content->getData('title')).'\';  $(\'title\').removeAttribute(\'readonly\'); $(\'title\').removeClassName(\'disabled\'); }"> <label for="use_default_title" class="inherit">'.Mage::helper('contentmanager')->__('Use default title').'</label></p>'
        ));
        
        $fieldset->addField('status', 'select' , array(
            'label'     => Mage::helper('contentmanager')->__('Status'),
            'required'  => true,
            'name'      => 'status',
            'values'    => array(
                0 => Mage::helper('contentmanager')->__('Disabled'),
                1 => Mage::helper('contentmanager')->__('Enabled')
            )
        ));
        $fieldset->addField('store_id', 'hidden' , array(
            'name'      => 'store_id',
        ));
        
        
        //get fieldsets
        $fieldsets = Mage::getModel('contentmanager/fieldset')
                        ->getCollection()
                        ->addFieldToFilter('ct_id', $contentTypeId);
        
        $fieldsets->getSelect()->order('sort_order', 'asc');
        
        
        foreach($fieldsets as $contentTypeFieldset)
        {
            $fieldset = $form->addFieldset('contenttype_form'.$contentTypeFieldset->getFieldsetId(), array('legend'=>$contentTypeFieldset->getTitle()));            
            
            if(!$hiddenCctIdAdded)
            {
                $fieldset->addField('ct_id', 'hidden', array(
                        'name'      => 'ct_id',
                ));  
                $hiddenCctIdAdded = true;
            }
            
            //add all fields for this fieldset
            $fields = Mage::getModel('contentmanager/contenttype_option')
                        ->getCollection();
            $fields->getSelect()->order('sort_order', 'asc');
            $fields->addFieldToFilter('fieldset_id', $contentTypeFieldset->getFieldsetId())
                        ->addTitleToResult($contentType->getStoreId())
                        ->addValuesToResult($contentType->getStoreId())
                        ->addFieldToFilter('ct_id', $contentTypeId);
            
            foreach($fields as $field)
            {
                //Add field date and datetime
                if($field->getType() == 'date' || $field->getType() == 'date_time')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                        'label'     => $field->getTitle(),
                        'required'  => $field->getIsRequire(),
                        'name'      => $field->getIdentifier(),
                        'note'      => $field->getNote(),
                        'image' => $this->getSkinUrl('images/grid-cal.gif'),
                        'format' => ($field->getType() == 'date_time') ? Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                        'time'    => ($field->getType() == 'date_time') ? true : false
                    ));
                }
                //Add field radio / drop_down / checkbox / multiple
                else if($field->getType() == 'radio' || $field->getType() == 'drop_down' || $field->getType() == 'multiple' || $field->getType() == 'checkbox')
                {
                    $values = array();
                    foreach($field->getValues() as $value)
                    {
                        $values[] = array(
                            'value'     => ($value->getValue()) ? $value->getValue() : $value->getTitle(),
                            'label'     => $value->getTitle()
                        );
                    }
                    
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                        'label'     => $field->getTitle(),
                        'required'  => ($field->getType() == 'radio') ? false : $field->getIsRequire(),
                        'name'      => $field->getIdentifier(),
                        'note'      => $field->getNote(),
                        'values' => $values
                    ));
                    
                    //set special renderer for checkbox type
                    if($field->getType() == 'checkbox')
                    {
                        //explode array
                        $content_data = Mage::registry('content_data');
                        $content_data->setData($field->getIdentifier(), explode(',', $content_data->getData($field->getIdentifier())));
                        
                        //new renderer
                        $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_checkbox'));
                    }
                }
                //Add field image
                else if($field->getType() == 'image')
                {
                    if(is_null($content->getData($field->getIdentifier())))
                    {
                        $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                        ));
                    }
                    else
                    {
                        $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            /*'required'  => $field->getIsRequire(),*/
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                        ));
                    }
                    
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_image'));
                }
                //Add field file
                else if($field->getType() == 'file')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                    ));
                
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_file'));
                }
                //Add field product
                else if($field->getType() == 'product')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                    ));
                
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_product'));
                }
                
                //Add field category
                else if($field->getType() == 'category')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                    ));
                
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_category'));
                }
                //Add field content
                else if($field->getType() == 'content')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                    ));
                
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_content'));
                }
                //Add field attribute
                else if($field->getType() == 'attribute')
                {
                    $fieldset->addField($field->getIdentifier(), $helper->getRendererTypeByFieldType($field->getType()), array(
                            'label'     => $field->getTitle(),
                            'required'  => $field->getIsRequire(),
                            'name'      => $field->getIdentifier(),
                            'note'      => $field->getNote()
                    ));
                
                    //new renderer
                    $form->getElement($field->getIdentifier())->setRenderer($this->getLayout()->createBlock('contentmanager/adminhtml_widget_form_renderer_fieldset_attribute'));
                }
                //Add field default
                else
                {
                    $wysiwygConfig = null;    // Default wysiwyg config
                    $type = $helper->getRendererTypeByFieldType($field->getType());    // Default type
                    if($field->getType() == 'area' && $field->getWysiwygEditor()){
                        
                        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
                        $wysiwygConfig['hidden'] = false;
                        $type = 'editor';
                    }
                    
                    $fieldset->addField($field->getIdentifier(), $type , array(
                        'label'     => $field->getTitle(),
                        'required'  => $field->getIsRequire(),
                        'name'      => $field->getIdentifier(),
                        'note'      => $field->getNote(),
                        'maxlength' => ($field->getMaxCharacters()) ? $field->getMaxCharacters() : null,
                        'config'    => $wysiwygConfig,
                    ));
                }
                
                //manage default values
                if($field->getType() == 'drop_down' || $field->getType() == 'multiple' || $field->getType() == 'checkbox' || $field->getType() == 'radio')
                {
                    $optionValues = $field->getValuesCollection();
                    foreach($optionValues as $optionValue)
                    {
                        if($optionValue->getDefault())
                        {
                            if($optionValue->getValue())
                            {
                                if(isset($default_values[$field->getIdentifier()]))
                                {
                                    $default_values[$field->getIdentifier()] .= ','.$optionValue->getValue();
                                }
                                else
                                {
                                    $default_values[$field->getIdentifier()] = $optionValue->getValue();
                                }
                            }
                            else
                            {
                                if(isset($default_values[$field->getIdentifier()]))
                                {
                                    $default_values[$field->getIdentifier()] .= ','.$optionValue->getTitle();
                                }
                                else
                                {
                                    $default_values[$field->getIdentifier()] = $optionValue->getTitle();
                                }
                            }
                        }
                    }
                    if($field->getType() != 'radio')
                    {
                        if(!isset($default_values[$field->getIdentifier()])) $default_values[$field->getIdentifier()] = '';
                        $default_values[$field->getIdentifier()] = explode(',', $default_values[$field->getIdentifier()]);                        
                    }
                }
                else if($field->getDefaultValue() !== null)
                {
                    if($field->getDefaultValue() == '<now>')
                    {
                        $date = new Zend_Date();
                        $field->setDefaultValue($date->toString('yyyy-MM-dd HH:mm:ss'));
                    }
                    $default_values[$field->getIdentifier()] = $field->getDefaultValue();
                }
            }
        }

        if ( Mage::getSingleton('adminhtml/session')->getContentData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContentData());
            Mage::getSingleton('adminhtml/session')->setContentData(null);
        }
        elseif ( Mage::registry('content_data') )
        {
            //not existing content id -> add default value
            if(!$entity_id)
            {
                $default_values['ct_id'] = $contentTypeId;
                $form->setValues(array_merge($default_values, array(
                    'title' => $contentType->getPageTitle(),
                    'store_id' => Mage::app()->getRequest()->getParam('store'),
                    'status' => $contentType->getDefaultStatus()
                )));
            }
            else
            {
                $values = $content->getData();
                $form->setValues(array_merge($values, array(
                    'title' => ((($content->getUseDefaultTitle() === '1' || !$entity_id) && $contentType->getPageTitle())?$contentType->getPageTitle():$content->getTitle()),
                    'store_id' => Mage::app()->getRequest()->getParam('store')
                )));                
            }
        }
        return parent::_prepareForm();
    }
}