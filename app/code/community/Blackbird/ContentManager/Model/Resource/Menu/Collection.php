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

class Blackbird_ContentManager_Model_Resource_Menu_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;
    protected $_storesIds = array();
    

    public function _construct()
    {
        parent::_construct();
        $this->_init('contentmanager/menu');
        $this->_map['fields']['store']   = 'store_table.store_id';
    }    

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }
    
    /**
     * Set store filter
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStoresIds($stores)
    {
        $this->_storesIds = $stores;
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|Mage_Core_Model_Store $storeId
     * @return Blackbird_ContentManager_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoresIds()
    {
        return $this->_storesIds;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Blackbird_ContentManager_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Perform operations after collection load
     *
     */
    protected function _afterLoad()
    {
        if (isset($this->_previewFlag) && $this->_previewFlag) {
            $items = $this->getColumnValues('menu_id');
            $connection = $this->getConnection();
            if (count($items)) {
                $select = $connection->select()
                        ->from(array('cps'=>$this->getTable('contentmanager/menu_store')))
                        ->where('cps.menu_id IN (?)', $items);

                if ($result = $connection->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('menu_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('menu_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('menu_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }
    
    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     */
    public function addStoreFilter($store = null, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            
            if($store === null)
            {
                $store = Mage::app()->getStore()->getId();
            }
            
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }
            

            $this->addFilter('store', array('in' => $store), 'public');
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('contentmanager/menu_store')),
                'main_table.menu_id = store_table.menu_id',
                array()
            )->group('main_table.menu_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }


    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }
    
    protected function _initSelect()
    {
        $allowedStoreIds=array();
        if(Mage::app()->getStore()->getId() == 0) //is in admin
        {
            $adminStoreFilter = Mage::app()->getRequest()->getParam('store');
            
            $allowedStoreIds = array(-1);
            $stores = Mage::getModel('core/store')->getCollection();
            
            if(Mage::helper('contentmanager')->isMenuAllowed(0))
            {
                $allowedStoreIds[] = 0;
            }
            
            foreach($stores as $store)
            {
                if(Mage::helper('contentmanager')->isMenuAllowed($store->getId()) && ($adminStoreFilter == $store->getId() || !$adminStoreFilter))
                {
                    $allowedStoreIds[] = $store->getId();
                }
            }
        }
        
        //filter
        $this->getSelect()->from(array('main_table' => $this->getMainTable()));

        if($allowedStoreIds)
        {
            $adapter        = $this->getConnection();
            $joinCondition  = array(
                'store.menu_id = main_table.menu_id'
            );
            $this->getSelect()
                ->joinLeft(
                    array('store' => $this->getTable('contentmanager/menu_store')),
                    implode(' AND ', $joinCondition),
                    array())            
                ->where($adapter->quoteInto('store.store_id IN ('.implode(',', $allowedStoreIds).')', (int) $this->getStoreId()))
                ->distinct();
        }
            
        return $this;     
    } 
    
}
