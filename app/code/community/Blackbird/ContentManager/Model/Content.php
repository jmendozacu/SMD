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

class Blackbird_ContentManager_Model_Content extends Blackbird_ContentManager_Model_Abstract
{
    const ENTITY = 'contenttype_content';
    const STATUS_ENABLED = 1;
    protected $_eventPrefix = 'blackbird_contenttype';
    protected $_eventObject = 'content';

    /**
     * Whether index events should be processed immediately
     *
     * @var bool
     */
    protected $_processIndexEvents = true;
    
    protected function _construct()
    {
        $this->_init('contentmanager/content');
    }


    /**
     * Delete content
     *
     * @return Blackbird_ContentManager_Model_Content
     */
    public function delete()
    {
        parent::delete();
        $this->setData(array());
        return $this;
    }

    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    public function __clone()
    {
        $this->setId(null);
    }

    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    /**
     * Get collection by content type identifier
     * @param type $type
     * @return type
     */
    public function getCollection($type = false)
    {
        if(!$type)
        {
            return parent::getCollection();
        }
        $collection = parent::getCollection();
        
        $contentType = Mage::getModel('contentmanager/contenttype')->load($type, 'identifier');
        if($contentType)
        {
            $collection->addAttributeToFilter('ct_id', $contentType->getId());
        }
        
        return $collection;
    }
    
    /**
     * Return content type model
     */
    public function getContentType()
    {
        return Mage::getModel('contentmanager/contenttype')->load($this->getCtId());
    }
    
    /**
     * Get Images Informations
     * $attributeName : attribute code name of the image wanted
     * $width: width in pixels, if we want a resized image, set to NULL for proportionnal resize with a setted $height
     * $height: height in pixels, if we want a resized image, set to NULL for proportionnal resize height a setted $width
     * $original : force to return the orignal instead of cropped image, false by default
     */
    public function getImage($attributeName, $width = NULL, $height = NULL, $original = false, $forceKeepAspectRatio = true)
    {
        $imageName = $this->getData($attributeName);
        $helper = Mage::helper('contentmanager');
        $option = $helper->getOptionByFieldIdentifier($attributeName, $this);
		
        if(!$imageName) return;
        
        //original (not cropped)
        $original_directoy = Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, $option->getFilePath()) . DS;
        $original_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $helper->getCtImageFolder() . '/' . str_replace(DS, '/', $option->getFilePath()) . '/';

        if($option->getCrop() && !$original)
        {
            //source (cropped if needed)
            $directoy = Mage::getBaseDir('media') . DS . $helper->getCtImageFolder() . DS . str_replace(array('/', '\\'), DS, $option->getFilePath()) . DS .$helper->getCtImageCroppedFolder() . DS;
            $src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $helper->getCtImageFolder() . '/' . str_replace(DS, '/', $option->getFilePath()) . '/' . $helper->getCtImageCroppedFolder() . '/';            
        }
        else
        {
            $directoy = $original_directoy;
            $src = $original_src;
        }
        
        //cropped haven't be processed yet
        if(!file_exists($directoy.$imageName))
        {
            $directoy = $original_directoy;
            $src = $original_src;
        }
        
        //return image if no resize
        if(!$width && !$height)
        {
            return $src.rawurlencode($imageName);
        }
        else
        {
            return str_replace('//', '/', str_replace(':/', '://', $helper->resize($directoy, $src, $imageName, $width, $height, $forceKeepAspectRatio)));
        }
    }
    

    /**
     * Get File path
     * $attributeName : attribute code name of the file wanted
     */
    public function getFile($attributeName)
    {
        $fileName = $this->getData($attributeName);
        $helper = Mage::helper('contentmanager');
        $option = $helper->getOptionByFieldIdentifier($attributeName, $this);
		
        if(!$fileName) return;
        $original_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $helper->getCtImageFolder() . '/' . str_replace(DS, '/', $option->getFilePath()) . '/';
        
        return str_replace('//', '/', str_replace(':/', '://', $original_src.$fileName));
    }    
    
    /**
     * Get Corresponding Title(s) for the option (type select)
     * $attributeName : attribute code name of the file wanted
     */
    public function getAttributeText($attributeName)
    {
        //get option
        $option = Mage::getModel('contentmanager/contenttype_option')->load($attributeName, 'identifier');
        
        //check if option type is select type
        if(!in_array($option->getType(), array('drop_down', 'radio', 'multiple', 'checkbox', 'attribute')))
        {
            return $this->getData($attributeName);
        }

        if($option->getType() == 'attribute')
        {
            //get title for each values
            $titles = array();
            foreach(explode(',', $this->getData($attributeName)) as $value)
            {
                $attribute_details = Mage::getSingleton("eav/config")->getAttribute("catalog_product", $option->getAttribute());
                $value = $attribute_details->getSource()->getOptionText($value);

                $titles[] = $value;
            }
            return implode(',', $titles);
        }
        else
        {
            //get title for each values
            $titles = array();
            foreach(explode(',', $this->getData($attributeName)) as $value)
            {
                $valueModel = Mage::getResourceModel('contentmanager/contenttype_option_value_collection')
                    ->addFieldToFilter('option_id', $option->getId())
                    ->addFieldToFilter('value', $value)
                    ->getValues($this->getStoreId());
                if($valueModel->getSize() == 0)
                {
                    $titles[] = $value;
                }
                else
                {
                    $titles[] = $valueModel->getFirstItem()->getTitle();
                }
            }
            return implode(',', $titles);
        }
    }    
    
    /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z/\.]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
    
    /**
     * Return url for current content
     */
    public function getUrl($storeId = null)
    {
        if((int) $storeId)
        {
            return Mage::getUrl($this->getUrlKey(), array('_direct' => $this->getUrlKey(), '_store' => (int) $storeId));
        }
        else
        {
            return Mage::getUrl($this->getUrlKey(), array('_direct' => $this->getUrlKey()));
        }
    }
    
    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->getData('store_id') === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->getData('store_id');
    }
    
    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Blackbird_ContentManager_Model_Content
     */
    public function setStoreId($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = Mage::app($storeId)->getStore()->getId();
        }
        $this->setData('store_id', $storeId);
        $this->getResource()->setStoreId($storeId);
        return $this;
    }
    
    /**
     * Check if a content is defined for a specific store_id
     * 
     */
    public function existsForStore($store_id)
    {
        return $this->getResource()->existsForStore($this->getId(), $store_id);
    }
    
    public function deleteCurrentStoreAttributes()
    {
        //delete attributes
        foreach($this->getData() as $key => $value)
        {
            if($key != 'store_id' && $key != 'entity_id' && $key != 'entity_type_id' && $key != 'ct_id' && $key != 'created_at' && $key != 'updated_at')
            {
                $this->getResource()->deleteCurrentStoreAttributes($this->getId(), $this->getStoreId(), $key);
            }
        }
    }
    
    /**
     * Delete link of current content to his store
     */
    public function deleteCurrentStoreLink()
    {
        //delete link to the store
        $this->getResource()->deleteCurrentStoreLink($this->getId(), $this->getStoreId());
    }
    

    /**
     * Reindex CatalogInventory save event
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        /** @var $indexer Mage_Index_Model_Indexer */
        $indexer = Mage::getSingleton('index/indexer');
        if ($this->_processIndexEvents) {
            $indexer->processEntityAction($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
        } else {
            $indexer->logEvent($this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE);
        }
        return $this;
    }
    
    /**
     * Render anything
     * @param mixed $element the element to render
     * @param array $params extra parameters
     */
    public function render($element, $params = null)
    {
        $option = null;
        $is_page_title = null;
        $layout = null;
        $storeId =  $this->getStoreId();
        
        if(is_string($element))
        {
            $option = Mage::getModel('contentmanager/contenttype_option')
                    ->getCollection()
                    ->addTitleToResult($storeId)
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('identifier', $element)
                    ->getFirstItem();
            $identifier = $element;
        }
        else if($element instanceof Blackbird_ContentManager_Model_Contenttype_Option)
        {
            $option = Mage::getModel('contentmanager/contenttype_option')
                    ->getCollection()
                    ->addTitleToResult($storeId)
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('identifier', $element->getIdentifier())
                    ->getFirstItem();
					
            $identifier = $element->getIdentifier();
        }
        else if($element instanceof Blackbird_ContentManager_Model_Contenttype_Layout_Field)
        {
            if(!$element->getOptionId())
            {
                $is_page_title = true;
                $identifier = 'title';
            }
            else
            {
                $option = Mage::getModel('contentmanager/contenttype_option')
                        ->getCollection()
                        ->addTitleToResult($storeId)
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('main_table.option_id', $element->getOptionId())
                        ->getFirstItem();
                $identifier = $option->getIdentifier();
            }
            $layout = $element;
        }
        else if($element instanceof Blackbird_ContentManager_Model_Contentlist_Layout_Field)
        {
            if(!$element->getOptionId())
            {
                $is_page_title = true;
                $identifier = 'title';
            }
            else
            {
                $option = Mage::getModel('contentmanager/contenttype_option')
                        ->getCollection()
                        ->addTitleToResult($storeId)
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('main_table.option_id', $element->getOptionId())
                        ->getFirstItem();
                $identifier = $option->getIdentifier();
            }
            $layout = $element;
        }
        else if($element instanceof Blackbird_ContentManager_Model_Contenttype_Layout_Abstract ||$element instanceof Blackbird_ContentManager_Model_Contentlist_Layout_Abstract )
        {
            $layout = $element;
            
        }
        
        //render option
        if($option || $is_page_title)
        {
            return $this->renderOption($identifier,
                array(
                    'option' => $option,
                    'params' => $params,
                    'layout' => $layout,
                )
            );
        }
        //render group
        elseif($layout && $layout->getType() == 'group')
        {
            return $this->renderLayoutGroup($layout, 
                array(
                    'params' => $params
                )
            );
        }
        //render group
        elseif($layout && $layout->getType() == 'block')
        {
            return $this->renderLayoutBlock($layout, 
                array(
                    'params' => $params
                )
            );
        }
    }
    
    /**
     * Render an option
     * @param type $identifier
     * @param type $params
     * @return type
     */
    public function renderOption($identifier, $params)
    {
        if(!isset($params['option']))
        {
            $type = 'field';
        }
        else
        {
            $option = $params['option'];
            $type = $option->getType();
        }
        
        $block = Mage::app()->getLayout()->createBlock(
                    'contentmanager/view_option',
                    NULL,
                    array(
                        'identifier' => $identifier,
                        'type' => $type,
                        'content' => $this,
                        'params' => $params
                    )
                );
        
        return $block->toHtml();
    }
    
    /**
     * Render a group of layout items
     * @param type $layoutGroup
     * @return type
     */
    public function renderLayoutGroup($layoutGroup, $params)
    {
        $result = '';
        
        //render header
        $block = Mage::app()->getLayout()->createBlock(
                    'contentmanager/view_group_header',
                    NULL,
                    array(
                        'layout_group' => $layoutGroup,
                        'content' => $this,
                        'params' => $params
                    )
                );
        
        $result .= $block->toHtml();
        
        //render childen
        foreach($layoutGroup->getChilds() as $layoutChild)
        {
            $result .= $this->render($layoutChild);
        }
        
        //render footer
        $block = Mage::app()->getLayout()->createBlock(
                    'contentmanager/view_group_footer',
                    NULL,
                    array(
                        'layout_group' => $layoutGroup,
                        'content' => $this,
                        'params' => $params
                    )
                );
        
        $result .= $block->toHtml();
        
        return $result;
    }
    
    /**
     * Render a layout cms block
     * @param type $layoutBlock
     */
    public function renderLayoutBlock($layoutBlock, $params)
    {
        $block = Mage::app()->getLayout()->createBlock(
                    'contentmanager/view_block',
                    NULL,
                    array(
                        'layout_block' => $layoutBlock,
                        'content' => $this,
                        'params' => $params
                    )
                );
        
        return $block->toHtml();
    }  
    
    /**
     * Get content collection
     * @param string $attributeCode attribute to based our selection in
     * @param array $attributes attributes to select for collection
     */
    public function getContentsCollection($attributeCode, $attributes = array())
    {
        $collection = Mage::getModel('contentmanager/content')
                        ->getCollection()
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToSelect(array_merge($attributes, array('title', 'url_key')));
        
        $collection->addAttributeToFilter('entity_id', array(
            'IN' => explode(',', str_replace(', ', ',', $this->getData($this->getData($attributeCode))))
        ));
        
        return $collection;
    }
}