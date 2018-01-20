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

class Blackbird_ContentManager_Block_Filter extends Mage_Core_Block_Template
{
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if(!$this->getTemplate())
        {
            $this->setTemplate('contenttype/filter.phtml');
        }
        
        return $this;
    }
    
    public function getAttributeValues()
    {
        $option = Mage::getModel('contentmanager/contenttype_option')->load($this->getAttributeToFilter(), 'identifier');
        
        if($option && in_array($option->getType(), array('drop_down', 'multiple', 'radio', 'checkbox')))
        {
            $collectionOptionsValues = Mage::getModel('contentmanager/contenttype_option_value')
                    ->getCollection()
                    ->addTitleToResult(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('option_id', $option->getId());

            $collectionOptionsValues->getSelect()->order('sort_order');
            return $collectionOptionsValues;
        }
		if($option && in_array($option->getType(), array('attribute')))
        {
			$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $option->getData('attribute'));
			if ($attribute->usesSource()) {
				$options = $attribute->getSource()->getAllOptions(false);
			}
			$results = array();
			foreach($options as $option)
			{
				$varienObject = new Varien_Object();
				$varienObject->setData(array(
					'title' => $option['label'],
					'value' => $option['value']
				));
				$results[] = $varienObject;
			}
			
			return $results;
        }
        
        return array();
    }
    
    public function getCurrentFilters()
    {
        $requestFilter = Mage::app()->getRequest()->getParam($this->getAttributeToFilter());
        if($requestFilter)
        {
            return explode(',', $requestFilter);
        }
        return array();
    }

    public function getFilteredUrl($params=array(), $forceBaseUrl = '*/*/*')
    {
        $urlParams = array();
        if($forceBaseUrl == '*/*/*')
        {
            $urlParams['_current']  = true;
            $urlParams['_escape']   = true;
            $urlParams['_use_rewrite']   = true;
        }
        
        if($this->getAllowMultiple() == 'true' && $params[$this->getAttributeToFilter()])
        {
            $currentFilters = $this->getCurrentFilters();
            //allow multiple filter
            if(in_array($params[$this->getAttributeToFilter()], $currentFilters))
            {
                //remove item
                if(($key = array_search($params[$this->getAttributeToFilter()], $currentFilters)) !== false)
                {
                    unset($currentFilters[$key]);
                }
                if(count($currentFilters) == 0)
                {
                    $urlParams['_query']    = array($this->getAttributeToFilter() => null);
                }
                else
                {
                    $urlParams['_query']    = array($this->getAttributeToFilter() => implode(',', $currentFilters));
                }
            }
            else
            {
                //add item
                $currentFilters[] = $params[$this->getAttributeToFilter()];
                $urlParams['_query']    = array($this->getAttributeToFilter() => implode(',', $currentFilters));
            }
        }
        else 
        {
            //only 1 filter
            //allow multiple filter
            if(in_array($params[$this->getAttributeToFilter()], $this->getCurrentFilters()))
            {
                //remove item
                $urlParams['_query']    = array($this->getAttributeToFilter() => null);
            }
            else
            {
                //set item
                $urlParams['_query']    = $params;
            }
        }
        
        return str_replace('%2C',',', $this->getUrl($forceBaseUrl, $urlParams));
    }
    
    public function getActiveState($value)
    {
        if(in_array($value, $this->getCurrentFilters()))
        {
            return true;
        }
        return false;
    }
    
}