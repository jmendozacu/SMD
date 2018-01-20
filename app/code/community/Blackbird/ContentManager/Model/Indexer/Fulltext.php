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

class Blackbird_ContentManager_Model_Indexer_Fulltext extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'contenttype_match_result';
    
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Blackbird_ContentManager_Model_Content::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );
    
    public function _construct(){
        parent::_construct();
        $this->_init('contentmanager/indexer_fulltext');
    }
    
    /**
     * Retrieve Indexer name
     * @return string
     */
    public function getName()
    {
        return Mage::helper('contentmanager')->__('CM - Content Search Index');
    }   
    
    
    /**
     * Retrieve Indexer description
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('contentmanager')->__('Index searchable attributes for searchable contents');
    }
 
    /**
     * Register data required by process in event object
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $dataObj = $event->getDataObject();
        if($event->getType() == Mage_Index_Model_Event::TYPE_SAVE){
            $event->addNewData('contenttype_update_entity_id', $dataObj->getId());
            $event->addNewData('contenttype_update_contenttype_id', $dataObj->getCtId());
        } elseif($event->getType() == Mage_Index_Model_Event::TYPE_DELETE){
            $event->addNewData('contenttype_delete_entity_id', $dataObj->getId());
        }
    }
 
    /**
     * Process event
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if(!empty($data['contenttype_update_entity_id'])){
            $this->_doUpdateContentIndex(array($data['contenttype_update_entity_id']), array($data['contenttype_update_contenttype_id']));
        }elseif(!empty($data['contenttype_delete_entity_id'])){
            $this->_doDeleteContentIndex($data['contenttype_delete_entity_id']);
        }
    }
 
 
    /**
     * match whether the reindexing should be fired
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        $entity = $event->getEntity();
        $result = true;
        if($entity != Blackbird_ContentManager_Model_Content::ENTITY){
            return;
        }
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);
        return $result;
    }
 
    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
	$this->doReindexAll();
    }
    
    /**
     * Update content fulltext index
     * @param array $entityIds
     */
    private function _doUpdateContentIndex($entityIds, $contentTypeIds)
    {
        $stores = Mage::getModel('core/store')->getCollection();
        $storeIds = array(0);
        foreach($stores as $store)
        {
            $storeIds[] = $store->getId();
        }
        
        foreach($entityIds as $key => $entityId)
        {
            //delete current index for all stores
            $fulltextIndexes = Mage::getModel('contentmanager/indexer_fulltext')
                    ->getCollection()
                    ->addFieldToFilter('entity_id', $entityId);

            foreach($fulltextIndexes as $fulltextIndex)
            {
                $fulltextIndex->delete();
            }

            //loop on all stores
            foreach($storeIds as $storeId)
            {
                $attributesToSelect = array('title');
                $attributesWeight = array('15');
                $searchData = array();

                //get attributes
                $options = Mage::getModel('contentmanager/contenttype_option')
                        ->getCollection()
                        ->addFieldToSelect('attribute_id')
                        ->addFieldToFilter('ct_id', $contentTypeIds[$key]);
                //foreach attribute
                foreach($options as $option)
                {
                    $attribute = Mage::getModel('contentmanager/attribute')->load($option->getAttributeId());
                    //check if they are searchable
                    if($attribute->getIsSearchable() && $attribute->getSearchAttributeWeight() > 0)
                    {
                        //save the attributes list
                        $attributesToSelect[] = $attribute->getAttributeCode();
                        $attributesWeight[] = $attribute->getSearchAttributeWeight();
                    }
                }

                //get the attribute value for current entityId
                if(count($attributesToSelect) > 0)
                {
                    $content = Mage::getModel('contentmanager/content')
                            ->setStoreId($storeId)
                            ->load($entityId);

                    if($content->existsForStore($storeId))
                    {
                        foreach($attributesToSelect as $keyName => $attributeCode)
                        {
                            if($content->getData($attributeCode))
                            {
                                $searchData[$attributeCode] = ltrim(str_repeat('|'.$content->getAttributeText($attributeCode), $attributesWeight[$keyName]), '|');
                            }
                        }
                        
                        //create new index
                        $fulltextIndex = Mage::getModel('contentmanager/indexer_fulltext');
                        $fulltextIndex->setStoreId($storeId);
                        $fulltextIndex->setEntityId($entityId);
                        $fulltextIndex->setDataIndex(join('|', $searchData));

                        $fulltextIndex->save();
                    }
                }
            }
        }
    }
    
    /**
     * Delete content fulltext index
     * @param int $entityId
     */
    private function _doDeleteContentIndex($entityId)
    {
        //delete current index for all stores
        $fulltextIndexes = Mage::getModel('contentmanager/indexer_fulltext')
                ->getCollection()
                ->addFieldToFilter('entity_id', $entityId);

        foreach($fulltextIndexes as $fulltextIndex)
        {
            $fulltextIndex->delete();
        }
    }
    
    /**
     * Update all content fulltext indexes
     * @param array $productIds
     */
    public function doReindexAll()
    {
        Mage::getResourceModel('contentmanager/indexer_fulltext')->truncate();
        
        $contentTypes = Mage::getModel('contentmanager/contenttype')
                ->getCollection()
                ->addFieldToFilter('search_enabled', 1);
        
        $contents = Mage::getModel('contentmanager/content')
                ->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('ct_id')
                ->addAttributeToFilter('ct_id', array('in' => $contentTypes->getAllIds()));
        
        foreach($contents as $content)
        {
            $this->_doUpdateContentIndex(array($content->getEntityId()), array($content->getCtId()));
        }
    }
}