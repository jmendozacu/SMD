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

class Blackbird_ContentManager_Block_List extends Mage_Core_Block_Template
{
    private $_filter = array();
    private $_show = array();
    private $_orderIdentifier;
    private $_orderOrder;
    private $_linkLabel;
    private $_linkPosition;
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $contentList = Mage::registry('current_contentlist');
        $ct = Mage::registry('current_ct');
        
        if($contentList && $contentList->getLayout() == 0)
        {
            //test applying list-ID.phtml
            $this->setTemplate('contenttype/list-'.$contentList->getId().'.phtml');
            if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
            {
                //test applying list-type.phtml
                $this->setTemplate('contenttype/list-'.$ct->getIdentifier().'.phtml');
                if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
                {
                    //applying default list.phtml
                    $this->setTemplate('contenttype/list.phtml');
                }
            }
        }
        elseif($contentList)
        {
            //test applying list/layout-ID.phtml
            $this->setTemplate('contenttype/list/layout-'.$contentList->getLayout().'.phtml');
            if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
            {
                //applying default view.phtml
                $this->setTemplate('contenttype/list.phtml');
            }
        }
        else
        {
            $this->setTemplate('contenttype/list.phtml');
        }        
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        //load contents collection
        
        //content list case
        //var_dump($contentList = Mage::registry('current_contentlist'));
        if(($contentList = Mage::registry('current_contentlist')) && $this->getNameInLayout() == 'contentmanager.list')
        {
            $collection = $this->getContentsCollectionByContentList($contentList);
            $this->setCollection($collection);
           
            //create pager
            $limit = $contentList->getLimitDisplay();
            
            $pager = $this->getLayout()->createBlock('page/html_pager', 'pager');
            $pager->setAvailableLimit(array($limit=>$limit));
            $pager->setCollection($this->getCollection());
            $this->setChild('pager', $pager);

            //load
            $this->getCollection()->load();
        }
        //widget or direct block call
        else
        {
            $collection = $this->getContentsCollection();
            $this->setCollection($collection);

            //create pager
            $limit = ($this->getLimit())?$this->getLimit():10;

            $pager = $this->getLayout()->createBlock('page/html_pager', 'pager');
            $pager->setAvailableLimit(array($limit=>$limit));
            $pager->setCollection($this->getCollection());
            $this->setChild('pager', $pager);

            //load
            $this->getCollection()->load();
        }        
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function setOrder($identifier, $order)
    {
        $this->_orderIdentifier = $identifier;
        $this->_orderOrder = $order;
    }
    
    public function addLink($label, $position)
    {
        $this->_linkLabel = $label;
        $this->_linkPosition = $position;
    }
    
    public function getLink()
    {
        if(!$this->_linkLabel) return null;
        
        return array(
            'label'     => $this->_linkLabel,
            'position'  => $this->_linkPosition
        );
    }
    
    public function addAttributeToFilter($identifier, $condition, $value)
    {
        if($identifier && $condition && $value)
        {
            $this->_filter[] = array(
                'identifier' => $identifier,
                'condition' => $condition,
                'value' => $value
            );
        }
    }
    
    public function addAttributeToShow($identifier, $params = null)
    {
        if($identifier)
        {
            $this->_show[] = array(
                'identifier' => $identifier,
                'params' => $params,
            );
        }
    }
    
    public function getAttributeToShow()
    {
        return $this->_show;
    }
    
    public function getContentsCollectionByContentList($contentList)
    {
        $collection = Mage::getModel('contentmanager/content')
                        ->getCollection()
                        ->addAttributeToFilter('ct_id', $contentList->getCtId())
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToSelect('*');
        
        //add filters
        foreach($this->_filter as $filter)
        {
            $collection->addAttributeToFilter($filter['identifier'], array($filter['condition'] => $filter['value']));
        }
        
        //add filters from url
        foreach($this->getRequest()->getParams() as $key => $param)
        {
            if(!in_array($key, array('page_id', 'p')))
            {
                $option = Mage::getModel('contentmanager/contenttype_option')->load($key, 'identifier');

                if($option && in_array($option->getType(), array('drop_down', 'multiple', 'radio', 'checkbox')))
                {
                    $findsetArray = array();
                    foreach(explode(',', $param) as $oneParam)
                    {
                        $findsetArray[] = array('attribute' => $key, array('finset' => $oneParam));
                    }
                    $collection->addAttributeToFilter($findsetArray); 
                }
                if($option && in_array($option->getType(), array('attribute')))
                {
                    $findsetArray = array();
                    foreach(explode(',', $param) as $oneParam)
                    {
                        $findsetArray[] = array('attribute' => $key, array('finset' => $oneParam));
                    }
                    $collection->addAttributeToFilter($findsetArray);          
                }
            }
        }
        
        //set order
        $collection->setOrder($contentList->getOrderField(), $contentList->getOrderBy());
        
        if($this->_orderIdentifier)
        {
            if(!$this->_orderOrder)
            {
                $order = 'ASC';
            }
            $collection->setOrder($this->_orderIdentifier, $this->_orderOrder);
        }
        
        return $collection;
    }    
    
    public function getContentsCollection()
    {
        $collection = Mage::getModel('contentmanager/content')
                        ->getCollection(strip_tags($this->getCtType()))
                        ->addAttributeToFilter('status', 1)
                        ->addAttributeToSelect('*');
        
        //add filters
        foreach($this->_filter as $filter)
        {
            $collection->addAttributeToFilter($filter['identifier'], array($filter['condition'] => $filter['value']));
        }
        
        //add filters from url
        foreach($this->getRequest()->getParams() as $key => $param)
        {
            if(!in_array($key, array('page_id', 'p')))
            {
                $option = Mage::getModel('contentmanager/contenttype_option')->load($key, 'identifier');

                if($option && in_array($option->getType(), array('drop_down', 'multiple', 'radio', 'checkbox')))
                {
                    $findsetArray = array();
                    foreach(explode(',', $param) as $oneParam)
                    {
                        $findsetArray[] = array('attribute' => $key, array('finset' => $oneParam));
                    }
                    $collection->addAttributeToFilter($findsetArray); 
                }
                if($option && in_array($option->getType(), array('attribute')))
                {
                    $findsetArray = array();
                    foreach(explode(',', $param) as $oneParam)
                    {
                        $findsetArray[] = array('attribute' => $key, array('finset' => $oneParam));
                    }
                    $collection->addAttributeToFilter($findsetArray);          
                }
            }
        }
        
        //set order
        $collection->setOrder('created_time', 'DESC');
        if($this->_orderIdentifier)
        {
            if(!$this->_orderOrder)
            {
                $order = 'ASC';
            }
            $collection->setOrder($this->_orderIdentifier, $this->_orderOrder);
        }
        
        return $collection;
    }
    
    /**
     * Get associated cms page if is set
     */
    public function getAssociatedCmsPage()
    {
        $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
        $page = null;
        if($pageId)
        {       
            $page = Mage::getModel('cms/page')
                        ->getCollection()
                        ->addFieldToFilter('page_id', $pageId)
                        ->addFieldToSelect('*')
                        ->getFirstItem();
        }
        
        return $page;
    }
    
    
    public function getLayoutItems()
    {
        $masterCollection = array();
        $contentList = Mage::registry('current_contentlist');
        
        
        //GROUPS
        $collection = Mage::getModel('contentmanager/contentlist_layout_group')
                ->getCollection()
                ->addFieldToFilter('parent_layout_group_id', array('null' => true))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $this->loadLayoutChildItems($layoutItem);
            
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //FIELDS
        $collection = Mage::getModel('contentmanager/contentlist_layout_field')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('null' => true))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }

        //BLOCKS
        $collection = Mage::getModel('contentmanager/contentlist_layout_block')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('null' => true))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');        
        
        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getColumn()][$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //Order by column and sort order
        ksort($masterCollection);
        foreach($masterCollection as $columnId => $columnCollection)
        {
            ksort($columnCollection);
            $masterCollection[$columnId] = $columnCollection;
        }
        
        return $masterCollection;
    }
    
    public function loadLayoutChildItems($layoutGroup)
    {
        $masterCollection = array();
        $contentList = Mage::registry('current_contentlist');
        
        //GROUPS
        $collection = Mage::getModel('contentmanager/contentlist_layout_group')
                ->getCollection()
                ->addFieldToFilter('parent_layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $this->loadLayoutChildItems($layoutItem);
            
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //FIELDS
        $collection = Mage::getModel('contentmanager/contentlist_layout_field')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');

        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }

        //BLOCKS
        $collection = Mage::getModel('contentmanager/contentlist_layout_block')
                ->getCollection()
                ->addFieldToFilter('layout_group_id', array('eq' => $layoutGroup->getId()))
                ->addFieldToFilter('cl_id', $contentList->getId());
        
        $collection->getSelect()->order('sort_order');        
        
        foreach($collection as $layoutItem)
        {
            $masterCollection[$layoutItem->getSortOrder()] = $layoutItem;
        }
        
        //Order by column and sort order
        ksort($masterCollection);
        
        $layoutGroup->setChilds($masterCollection);
    }
    
}