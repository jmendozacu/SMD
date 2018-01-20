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

class Blackbird_ContentManager_Block_Adminhtml_Content_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setTemplate('blackbird/contentmanager/grid/grid.phtml');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
 
    protected function _prepareCollection($type = false)
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        
        //if has filter from store id 0
        if(Mage::app()->getRequest()->getParam('filter') && $storeId == 0)
        {
            $stores = Mage::helper('contentmanager')->getStores();
            $itemIds = array();
            foreach($stores as $store)
            {
                if($type && Mage::helper('contentmanager')->isViewAllowed($store->getId(), $this->getRequest()->getParam('ct_id')))
                {
                    $collection = Mage::getModel('contentmanager/content')
                        ->getCollection($type)
                        ->addAttributeToSelect('entity_id');
                    
                    if($this->getRequest()->getParam('ct_id')) $collection->addAttributeToFilter('ct_id', $this->getRequest()->getParam('ct_id'));

                    $collection->addStoreFilter($store->getId());
                    $this->setCollection($collection);
                    $this->_prepareCollectionLight();
                    
                    $itemIds = array_merge($itemIds, $collection->getAllIds());
                }
                else
                {
                    $contentTypes = Mage::getModel('contentmanager/contenttype')->getCollection();
                    foreach($contentTypes as $contentType)
                    {
                        if(Mage::helper('contentmanager')->isViewAllowed($store->getId(), $contentType->getId()))
                        {
                            $collection = Mage::getModel('contentmanager/content')
                                    ->getCollection()
                                    ->addAttributeToFilter('ct_id', $contentType->getId())
                                    ->addAttributeToSelect('entity_id');

                            if($this->getRequest()->getParam('ct_id')) $collection->addAttributeToFilter('ct_id', $this->getRequest()->getParam('ct_id'));

                            $collection->addStoreFilter($store->getId());
                            $this->setCollection($collection);
                            $this->_prepareCollectionLight();

                            $itemIds = array_merge($itemIds, $collection->getAllIds());
                        }						
                    }
                }
            }
            
            $masterCollection = Mage::getModel('contentmanager/content')
                        ->getCollection($type)
                        ->addAttributeToFilter('entity_id', array('in' => $itemIds))
                        ->addAttributeToSelect('entity_id');
            
            $this->setCollection($masterCollection);
            $this->_prepareCollectionLightDirection();

            //load attributes value for selected store view
            foreach($masterCollection as $item)
            {
                $item = $item->setStoreId($this->_getStore()->getId())->load(null);
            }
        }
        else
        {
            $collection = Mage::getModel('contentmanager/content')
                ->getCollection($type)
                ->addAttributeToSelect('entity_id');
            
            if($this->getRequest()->getParam('ct_id')) $collection->addAttributeToFilter('ct_id', $this->getRequest()->getParam('ct_id'));

            if($storeId)
            {
                $collection->addStoreFilter($storeId);
            }

            $this->setCollection($collection);
            parent::_prepareCollection();

            //load attributes value for selected store view
            foreach($collection as $item)
            {
                $item = $item->setStoreId($this->_getStore()->getId())->load(null);
            }
        }
        
        return $this;
    }
    

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollectionLight()
    {
        if ($this->getCollection()) {

            $filter   = $this->getParam($this->getVarNameFilter(), null);
            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }
            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
            }
        }

        return $this;
    }    

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollectionLightDirection()
    {
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }    
 
    public function getStoreIterations($_item)
    {
        $contentResource = Mage::getSingleton('contentmanager/content')->getResource();
        $adminStoreFilter = Mage::app()->getRequest()->getParam('store');
        
        $storeArray = $contentResource->getAllExistingViewableStores($_item, $adminStoreFilter);
        
        return $storeArray;
    }
}