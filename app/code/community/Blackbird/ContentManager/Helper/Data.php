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

class Blackbird_ContentManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $loadedContents = array();
    public $existingStore = array();
    private $_stores = null;    
    
    const CT_IMAGE_FOLDER = 'contenttype';
    const CT_IMAGE_CROPPED_FOLDER = 'crop';
    
    public function getCtImageFolder()
    {
        return Blackbird_ContentManager_Helper_Data::CT_IMAGE_FOLDER;
    }
    
    public function getCtImageCroppedFolder()
    {
        return Blackbird_ContentManager_Helper_Data::CT_IMAGE_CROPPED_FOLDER;
    }
    
    /**
     * Return attribute type corresponding to contenttype type / For EAV storage
     */
    public function getAttributeTypeByFieldType($field_type)
    {
        $fieldTypeToDataType = array(
            'field' => 'varchar',
            'area' => 'text',
            'password' => 'varchar',
            'file' => 'varchar',
            'image' => 'varchar',
            'image_dimensions' => 'text',
            'img_titl' => 'varchar',
            'img_alt' => 'varchar',
            'img_url' => 'varchar',
            'drop_down' => 'varchar',
            'radio' => 'varchar',
            'checkbox' => 'text',
            'multiple' => 'text',
            'date' => 'datetime',
            'date_time' => 'datetime',
            'product'    => 'text',
            'category'    => 'text',
            'content'    => 'text',
            'int'    => 'int',
        );
        
        return (isset($fieldTypeToDataType[$field_type]))?$fieldTypeToDataType[$field_type]:'text';
    }
    
    /**
     * Return frontend renderer type corresponding to contenttype type / For render in FORM (when creating new content)
     */
    public function getRendererTypeByFieldType($field_type)
    {
        $fieldTypeToDataType = array(
            'field' => 'text',
            'area' => 'textarea',
            'password' => 'password',
            'file' => 'file',
            'image' => 'image',
            'drop_down' => 'select',
            'radio' => 'radios',
            'checkbox' => 'checkboxes',
            'multiple' => 'multiselect',
            'date' => 'date',
            'date_time' => 'date',
            'int' => 'text',
        );
        
        return (isset($fieldTypeToDataType[$field_type]))?$fieldTypeToDataType[$field_type]:'text';
    }
    
    /**
     * 
     * @param string $identifier
     * @param Blackbird_ContentManager_Model_Content || integer $content (i.e. can be content_id or content Model)
     * 
     * @return Blackbird_ContentManager_Model_ContentType_Option
     * 
     * @todo Implement looking for option when content is null 
     */
    
    public function getOptionByFieldIdentifier($identifier, $content = null){
        
        if(is_numeric($content)){
            $content = Mage::getModel('contentmanager/content')->load($content);
        }
        
        //new content
        if($content === null)
        {
            $contentType = Mage::registry('current_contenttype');
        }
        else
        {
            $contentType = $content->getContentType();
        }
        
        foreach($contentType->getOptions() as $option){
            if($option->getIdentifier() == $identifier){
                break;
            }
        }
        
        return $option;
    }
    

    /**
     * Resize Image proportionally and return the resized image url
     *
     * @param string $imageName         name of the image file
     * @param integer|null $width       resize width
     * @param integer|null $height      resize height
     * @param string|null $imagePath    directory path of the image present inside media directory
     * @return string               full url path of the image
     */
    public function resize($imageDirectory, $imageSrc, $imageName, $width=NULL, $height=NULL, $forceKeepAspectRatio=TRUE)
    {
        $resizePath = 'cache' . DS . $width . 'x' . $height;
        $resizeSrc = 'cache' . '/' . $width . 'x' . $height;
        
        $resizeDirectory = $imageDirectory . $resizePath . DS . $imageName;

        try {
            if (file_exists($imageDirectory) && !file_exists($resizeDirectory)) {
                $imageObj = new Varien_Image($imageDirectory.$imageName);
                $imageObj->constrainOnly(TRUE);
                $imageObj->quality(89);
                $imageObj->keepAspectRatio($forceKeepAspectRatio);
                $imageObj->keepTransparency(TRUE);
                $imageObj->resize($width,$height);
                $imageObj->save($resizeDirectory);
            }
        }
        catch(Exception $e)
        {
            Mage::logException($e);
        }
        return $imageSrc . $resizeSrc . '/' . rawurlencode($imageName);
    }
    
    /**
     * Replace {{.*}} patterns in data
     * @param string $data
     * @param Content $content
     * @return string
     */
    public function applyPattern($data, $content)
    {
        $matches = array();
        preg_match_all('/{{([a-zA-Z0-9_\|]*)}}/', (string) $data, $matches);

        if(!empty($matches[1]))
        {
            foreach($matches[1] as $key => $replacement)
            {
                $attributeContent = $content->getData($replacement);
                if(preg_match('/\|plain/', $replacement))
                {
                    $replacement = str_replace('|plain', '', $replacement);
                    $attributeContent = $this->_getPlainValue($content->getData($replacement));
                }
                elseif(preg_match('/\|date_short/', $replacement))
                {
                    $replacement = str_replace('|date_short', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatDate($content->getData($replacement), 'short', false);
                }
                elseif(preg_match('/\|date_medium/', $replacement))
                {
                    $replacement = str_replace('|date_medium', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatDate($content->getData($replacement), 'medium', false);
                }
                elseif(preg_match('/\|date_long/', $replacement))
                {
                    $replacement = str_replace('|date_long', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatDate($content->getData($replacement), 'long', false);
                }
                elseif(preg_match('/\|datetime_short/', $replacement))
                {
                    $replacement = str_replace('|datetime_short', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatTime($content->getData($replacement), 'short', true);
                }
                elseif(preg_match('/\|datetime_medium/', $replacement))
                {
                    $replacement = str_replace('|datetime_medium', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatTime($content->getData($replacement), 'medium', true);
                }
                elseif(preg_match('/\|datetime_long/', $replacement))
                {
                    $replacement = str_replace('|datetime_long', '', $replacement);
                    $attributeContent = Mage::helper('core')->formatTime($content->getData($replacement), 'long', true);
                }
                $data = str_replace($matches[0][$key], $attributeContent, $data);
            }
        }
        
        return $data;
    }
    
    /**
     * Get plain value for a content
     */
    private function _getPlainValue($str)
    {
        return strip_tags($str);
    }
    
    /**
     * Return forbidden identifier for field identifier
     * @return array
     */
    public function getForbiddenIdentifier()
    {
        $result = array(
                'entity_id',
                'entity_type_id',
                'ct_id',
                'created_at',
                'updated_at',
                'content',
                'title',
                'meta_title',
                'description',
                'keywords',
                'robots',
                'og_title',
                'og_url',
                'og_description',
                'og_image',
                'og_type',
                'use_default_title',
                'use_default_description',
                'use_default_keywords',
                'use_default_robots',
                'use_default_og_title',
                'use_default_og_url',
                'use_default_og_description',
                'use_default_og_image',
                'use_default_og_type',
                'status',
                'store'
            );
        return $result;
    }
    
    public function getContentsByOptionIds($ids = null, $attribute_code = null, $content_type = null, $condition = 'or')
    {
        $collection = Mage::getModel('contentmanager/content')
                ->getCollection($content_type)
                ->addAttributeToSelect('*');
                
        if($ids && $attribute_code && $condition == 'or')
        {
            $arrayFilter = array();
            foreach(explode(',', $ids) as $id)
            {
                $arrayFilter[] = array(
                    'attribute' => $attribute_code,
                    'finset' => $id
                );
            }
            $collection->addAttributeToFilter($attribute_code, $arrayFilter);
        }
        else if(is_array($ids) && $attribute_code && $condition == 'and')
        {
            foreach($ids as $id)
            {
                $collection->addAttributeToFilter($attribute_code, array('finset' => $id));
            }
        }
        
        $collection->addAttributeToFilter('status', 1);
        
        return $collection;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    public function isEditAllowed($storeId, $cctId)
    {
        $contentTypeModel = Mage::registry('current_contenttype');
        
        if(!$contentTypeModel)
        {
            $identifier = Mage::getResourceModel('contentmanager/contenttype')->getIdentifierById($cctId);
        }
        else
        {
            $identifier = $contentTypeModel->getIdentifier();
        }
        
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$identifier.'_edit_'.$storeId) || Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$identifier.'_edit_0') || Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_everything');
    }  

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    public function isViewAllowed($storeId, $cctId)
    {
        $contentTypeModel = Mage::registry('current_contenttype');
        
        if(!$contentTypeModel)
        {
            $identifier = Mage::getResourceModel('contentmanager/contenttype')->getIdentifierById($cctId);
        }
        else
        {
            $identifier = $contentTypeModel->getIdentifier();
        }
        
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$identifier.'_view_'.$storeId) || Mage::getSingleton('admin/session')->isAllowed('contentmanager/content_'.$identifier.'_view_0') || $this->isEditAllowed($storeId, $cctId);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    public function isMenuAllowed($storeId)
    {
        return Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_menu_'.$storeId) || Mage::getSingleton('admin/session')->isAllowed('contentmanager/manage_menu_0');
    }
    
    /**
     * Get stores once
     */
    public function getStores()
    {
        if(!$this->_stores)
        {
            $this->_stores = Mage::getModel('core/store')->getCollection();
        }
        
        return $this->_stores;
    }
    
    public function getContentModel($storeId, $contentId)
    {
        if(!isset($this->loadedContents[$storeId.'_'.$contentId]))
        {
            $contentTypeModel = Mage::registry('current_contenttype');
            if($contentTypeModel)
            {
                $fieldsToSelect = $contentTypeModel->getGridAttributes();                
            }
            else
            {
                $fieldsToSelect = array();
            }
            
            $this->loadedContents[$storeId.'_'.$contentId] = 
                    Mage::getModel('contentmanager/content')
                        ->setStoreId($storeId)
                        ->getCollection()
                        ->addAttributeToSelect(array_merge(array('title', 'url_key', 'status', 'created_at', 'updated_at'), $fieldsToSelect))
                        ->addAttributeToFilter('entity_id', $contentId)
                        ->getFirstItem();
        }
        return $this->loadedContents[$storeId.'_'.$contentId];
    }    
        
}