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

class Blackbird_ContentManager_Block_Search_Result extends Mage_Core_Block_Template
{
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if(!$this->getTemplate())
        {
            $this->setTemplate('contenttype/search/results.phtml');
        }
        
        //load contents collection
        $collection = $this->getContentsCollection();
        $this->setCollection($collection);
        
        //Registry current content collection 
        $allCtIds = $this->getCollection()->getAllCtIds();
        $countCtIds = array_count_values($allCtIds);
        
        Mage::register('all_ct_id', $allCtIds, true);
        Mage::register('count_ct_id', $countCtIds, true);
        
        return $this;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        
        //limit
        $limit = ($this->getLimit())?$this->getLimit():20;
        
        //set collection content type filter
        $currentCt = Mage::app()->getRequest()->getParam('ct');
        $currentCt = explode(',', $currentCt);
        if(isset($currentCt[0]) && $currentCt[0] === '') unset($currentCt[0]);
        
        if($currentCt && is_array($currentCt) && count($currentCt) > 0)
        {
            $cctIds = Mage::getModel('contentmanager/contenttype')->getCollection()->addFieldToFilter('identifier', array('in' => $currentCt))->getAllIds();
            $this->getCollection()->addAttributeToFilter('ct_id', array('in' => $cctIds));
        }
        
        //create pager
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
    
    public function getContentsCollection()
    {
        $collection = Mage::getModel('contentmanager/indexer_fulltext')
                                            ->getCollection();

        $query = Mage::helper('catalogsearch')->getQuery();
        //$query->getQueryText();
        //$query->getPopularity()
        //$query->getNumResults()
        
        $collection->addBindParam(':query', $query->getQueryText());
        $field = new Zend_Db_Expr("MATCH (main_table.data_index) AGAINST (:query IN BOOLEAN MODE)");
        $collection->getSelect()
                ->columns(array('relevance' => $field))
                ->where('MATCH (main_table.data_index) AGAINST (:query IN BOOLEAN MODE)') // @see http://dev.mysql.com/doc/refman/5.0/fr/fulltext-search.html 
                ->order('relevance DESC');

        $entities = array();
        foreach($collection as $results){
            $entities[] = $results->getEntityId();
        }
        
        $contents = Mage::getModel('contentmanager/content')
                ->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $entities))
                ->addFieldToFilter('status', 1);

        return $contents;
    }
    
    
}