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

class Blackbird_ContentManager_Block_Widget_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
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
        
        //init values
        if($this->getOrderField())
        {
            $this->setOrder($this->getOrderField(), $this->getOrderType());
        }
        if($this->getContentType())
        {
            $this->setCtType($this->getContentType());
        }
        if($this->getData('attributes_to_show'))
        {
            $lines = explode('##', $this->getData('attributes_to_show'));
            foreach($lines as $line)
            {
                $values = explode('||', $line);
                if(count($values) > 1)
                {
                    $this->addAttributeToShow($values[0], array(
                        'label' => $values[1],
                        'label_type' => $values[2],
                        'html_label_tag' => $values[3],
                        'html_tag' => $values[4],
                        'html_id' => $values[5],
                        'html_class' => $values[6],
                        'has_link' => $values[7],
                        'type' => $values[8],
                        'width' => $values[9],
                        'height' => $values[10],
                        'link' => $values[11],
                    ));
                }
            }
        }
        if($this->getData('attributes_to_filter'))
        {
            $lines = explode('##', $this->getData('attributes_to_filter'));
            foreach($lines as $line)
            {
                $values = explode('||', $line);
                if(count($values) > 1)
                {
                    $this->addAttributeToFilter($values[0], $values[1], $values[2]);
                }
            }
        }
        
        if(!$this->getTemplate())
        {
            //test applying list-type.phtml
            $this->setTemplate('contenttype/list-'.$this->getContentType().'.phtml');
            if(!file_exists(Mage::getBaseDir('app') . DS . 'design' . DS . $this->getTemplateFile()))
            {
                //applying default list.phtml
                $this->setTemplate('contenttype/list.phtml');
            }
        }
        
        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        //load contents collection
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
    
    public function addAttributeToShow($identifier, $params)
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
    
}