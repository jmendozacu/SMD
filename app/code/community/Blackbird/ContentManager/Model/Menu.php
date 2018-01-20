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

class Blackbird_ContentManager_Model_Menu extends Mage_Core_Model_Abstract
{
    private $_treeNodes = array();
    private $_urls = array();
    
    public function _construct(){
        parent::_construct();
        $this->_init('contentmanager/menu');
    }
    
    public function getTreeNodes($status = false){

        if(empty($this->_treeNodes)){
            $this->setTreeNodes($status);
        }
        return $this->_treeNodes;
    }

    public function getNodes($status){
        $nodes = Mage::getModel('contentmanager/menu_node')
                        ->getCollection()
                        ->addFieldToFilter('menu_id', array('eq' => $this->getId()));

        $nodes->getSelect()->order('position');
        if($status === true)
        {
            $nodes->addFieldToFilter('status', 1);
        }

        return $nodes;
    }

    protected function setTreeNodes($status){
        $this->_treeNodes = $this->getTree(null, $this->getNodes($status));
    }

    /**
     * Format tree menu in array
     * @param int $level // Level to begin (Used for recursivity)
     * @param ContentType_Menu_Node_Collection $nodes
     */
    protected function getTree($parent_id = null, $nodes){

        $result = array();
        foreach ($nodes as $node) {
            if ($parent_id == $node['parent_id']) {

                $data = $node->getData();
                $data['children'] = $this->getTree($node['node_id'], $nodes);

                $result[] = $data;
            }

        }
        return $result;
    }

    /**
     * Render menu item
     */
    public function render($node)
    {
        //active class
        $node['active'] = false;
        if($this->isActive($node))
        {
            $node['classes'] .= ' menu-item-active';
            $node['active'] = true;
        }
        
        //other classes
        if($node['children_count'] > 0)
        {
            $node['classes'] .= ' menu-has-children';
            $node['classes'] .= ' menu-children-count-'.$node['children_count'];
        }
        $node['classes'] .= ' menu-type-'.$node['type'];
        $node['classes'] .= ' menu-position-'.$node['position'];

        //render node
        $block = Mage::app()->getLayout()->createBlock(
                    'contentmanager/menu_item',
                    NULL,
                    array(
                        'type' => $node['type'],
                        'node' => $node,
                        'menu' => $this
                    )
                ); 

        return $block->toHtml();
    }
    
    
    public function isActive($node)
    {
        $url1 = parse_url($this->getUrl($node));
        $url2 = parse_url(Mage::helper('core/url')->getCurrentUrl());
	$category = Mage::registry('current_category');
		
        if((!isset($url1['host']) || !$url1['host'] || !isset($url2['host']) || !$url2['host'] || $url1['host'] == $url2['host']) && (isset($url1['path']) && isset($url2['path']) && rtrim($url1['path'], '/') == rtrim($url2['path'], '/')))
        {
            return true;
        }
        else if($node['type'] == 'category' && $category && $category->getId() == $node['entity_id'])
        {
            return true;
        }
        
        if($node['children_count'] > 0)
        {
            foreach($node['children'] as $child)
            {
                if($this->isActive($child))
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function getUrl($node)
    {
        if(!isset($this->_urls[$node['node_id']]))
        {
            $url = '';
            switch($node['type'])
            {
                case 'category':
                    $entity_id = $node['entity_id'];
                    $category = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('url_key')->addAttributeToFilter('entity_id', array('in' => explode(', ', $entity_id)));
                    if($category->getSize() > 0)
                    {
                        $url = $category->getLastItem()->getUrl();
                    }
                    break;
                case 'content':
                    $entity_id = $node['entity_id'];
                    $content = Mage::getModel('contentmanager/content')->getCollection()->addAttributeToSelect('url_key')->addAttributeToFilter('entity_id', $entity_id);
                    if($content->getSize() > 0)
                    {
                        $contentType = $content->getFirstItem()->getContentType();
                        if($contentType->getUrlMenu() == 1)
                        {
                            //check if we have menu URL
                            $nodes = Mage::getModel('contentmanager/menu_node')
                                ->getCollection()
                                ->addFieldToSelect('*')
                                ->addFieldToFilter('type', 'content')
                                ->addFieldToFilter('menu_id', $this->getId())
                                ->addFieldToFilter('entity_id', $content->getFirstItem()->getId());

                            if($nodes->getSize() > 0 && $nodes->getFirstItem()->getUrlPath())
                            {
                                $url = Mage::getUrl($nodes->getFirstItem()->getUrlPath(), array('_direct' => $nodes->getFirstItem()->getUrlPath()));
                            }
                            else
                            {
                                $url = $content->getFirstItem()->getUrl();
                            }
                        }
                        else
                        {
                            $url = $content->getFirstItem()->getUrl();
                        }
                    }
                    break;
                case 'page':
                    $entity_id = $node['entity_id'];
                    $page = Mage::getModel('cms/page')->getCollection()->addFieldToSelect('identifier')->addFieldToFilter('identifier', $entity_id);
                    if($page->getSize() > 0)
                    {
                        $url = Mage::getUrl($page->getFirstItem()->getIdentifier());
                    }                    
                    break;
                case 'product':
                    $sku = $node['entity_id'];
                    $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('url_key')->addAttributeToFilter('sku', $sku);
                    if($product->getSize() > 0)
                    {
                        $url = $product->getFirstItem()->getProductUrl();
                    }                    
                    break;
                case 'custom':
                    $format = unserialize($node['format']);
                    $url = $format['url'];                    
                    break;
                case 'node':
                    $entity_id = $node['entity_id'];
                    $format = unserialize($node['format']);

                    $url = '';

                    if($format['firstchild'] == 1 && count($node['children']) > 0)
                    {
                        $url = $this->getUrl('', array('_direct' => $node['children'][0]));
                    }
                    break;
            }
            $this->_urls[$node['node_id']] = $url;
        }

        return $this->_urls[$node['node_id']];
    }

    public function getLabel($node)
    {
        $label = $node['label'];
        if(!$label)
        {
            switch($node['type'])
            {
                case 'category':
                    $entity_id = $node['entity_id'];
                    $category = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in' => explode(', ', $entity_id)));
                    if($category->getSize() > 0)
                    {
                        $label = $category->getLastItem()->getName();
                    }
                    break;
                case 'content':
                    $entity_id = $node['entity_id'];
                    $content = Mage::getModel('contentmanager/content')->getCollection()->addAttributeToSelect('title')->addAttributeToFilter('entity_id', $entity_id);
                    if($content->getSize() > 0)
                    {
                        $label = $content->getFirstItem()->getTitle();
                    }                    
                    break;
                case 'page':
                    $entity_id = $node['entity_id'];
                    $page = Mage::getModel('cms/page')->getCollection()->addFieldToSelect('content_heading')->addFieldToFilter('identifier', $entity_id);
                    if($page->getSize() > 0)
                    {
                        if($page->getFirstItem()->getContentHeading())
                        {
                            $label = $page->getFirstItem()->getContentHeading();
                        }
                    }                    
                    break;
                case 'product':
                    $sku = $node['entity_id'];
                    $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name')->addAttributeToFilter('sku', $sku);
                    if($product->getSize() > 0)
                    {
                        $label = $product->getFirstItem()->getName();
                    }
                    break;
            }
        }

        return $label;
    }        
}