<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Blackbird_ContentManager
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Blackbird_ContentManager_Model_Content_Indexer_Flat extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'contentmanager_content_flat_match_result';

    /**
     * Index math Entities array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Blackbird_ContentManager_Model_Content::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Blackbird_ContentManager_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Blackbird_ContentManager_Model_Content_Flat_Indexer::ENTITY => array(
            Blackbird_ContentManager_Model_Content_Flat_Indexer::EVENT_TYPE_REBUILD,
        ),
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        /** @var $contentFlatHelper Blackbird_ContentManager_Helper_Content_Flat */
        $contentFlatHelper = Mage::helper('contentmanager/content_flat');
        return $contentFlatHelper->isEnabled() || !$contentFlatHelper->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('contentmanager')->__('CM - Content Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('contentmanager')->__('Reorganize EAV content structure to flat structure');
    }

    /**
     * Retrieve ContentManager Content Flat Indexer model
     *
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('contentmanager/content_flat_indexer');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat contentmanager content is enabled and specific save
     * attribute, store, store_group
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        /** @var $contentFlatHelper Blackbird_ContentManager_Helper_Content_Flat */
        $contentFlatHelper = Mage::helper('contentmanager/content_flat');
        if (!$contentFlatHelper->isEnabled() || !$contentFlatHelper->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Blackbird_ContentManager_Model_Resource_Eav_Attribute::ENTITY) {
            /* @var $attribute Blackbird_ContentManager_Model_Resource_Eav_Attribute */
            $attribute      = $event->getDataObject();
            $addFilterable  = $contentFlatHelper->isAddFilterableAttributes();

            $enableBefore   = $attribute && (($attribute->getOrigData('backend_type') == 'static')
                || ($addFilterable && $attribute->getOrigData('is_filterable') > 0)
                || ($attribute->getOrigData('used_in_content_listing') == 1)
                || ($attribute->getOrigData('is_used_for_promo_rules') == 1)
                || ($attribute->getOrigData('used_for_sort_by') == 1));

            $enableAfter    = $attribute && (($attribute->getData('backend_type') == 'static')
                || ($addFilterable && $attribute->getData('is_filterable') > 0)
                || ($attribute->getData('used_in_content_listing') == 1)
                || ($attribute->getData('is_used_for_promo_rules') == 1)
                || ($attribute->getData('used_for_sort_by') == 1));

            if ($attribute && $event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = $enableBefore;
            } elseif ($attribute && $event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                if ($enableAfter || $enableBefore) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Store::ENTITY) {
            if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = true;
            } else {
                /* @var $store Mage_Core_Model_Store */
                $store = $event->getDataObject();
                if ($store && $store->isObjectNew()) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        } else if ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            /* @var $storeGroup Mage_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Blackbird_ContentManager_Model_Content::ENTITY:
                $this->_registerContentManagerContentEvent($event);
                break;
            case Mage_Core_Model_Store::ENTITY:
                if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
            case Blackbird_ContentManager_Model_Resource_Eav_Attribute::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $event->addNewData('contentmanager_content_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
            case Blackbird_ContentManager_Model_Content_Flat_Indexer::ENTITY:
                switch ($event->getType()) {
                    case Blackbird_ContentManager_Model_Content_Flat_Indexer::EVENT_TYPE_REBUILD:
                        $event->addNewData('id', $event->getDataObject()->getId());
                }
                break;
        }
    }

    /**
     * Register data required by contentmanager content process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Blackbird_ContentManager_Model_Content_Indexer_Flat
     */
    protected function _registerContentManagerContentEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $content Blackbird_ContentManager_Model_Content */
                $content = $event->getDataObject();
                $event->addNewData('contentmanager_content_flat_content_id', $content->getId());
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                /* @var $actionObject Varien_Object */
                $actionObject = $event->getDataObject();

                $reindexData  = array();
                $reindexFlat  = false;

                // check if status changed
                $attrData = $actionObject->getAttributesData();
                if (isset($attrData['status'])) {
                    $reindexFlat = true;
                    $reindexData['contentmanager_content_flat_status'] = $attrData['status'];
                }

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $reindexFlat = true;
                    $reindexData['contentmanager_content_flat_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['contentmanager_content_flat_action_type'] = $actionObject->getActionType();
                }

                $flatAttributes = array();
                if (is_array($attrData)) {
                    $flatAttributes = array_intersect($this->_getFlatAttributes(), array_keys($attrData));
                }

                if (count($flatAttributes) > 0) {
                    $reindexFlat = true;
                    $reindexData['contentmanager_content_flat_force_update'] = true;
                }

                // register affected contents
                if ($reindexFlat) {
                    $reindexData['contentmanager_content_flat_content_ids'] = $actionObject->getContentIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Register core store delete process
     *
     * @param Mage_Index_Model_Event $event
     * @return Blackbird_ContentManager_Model_Content_Indexer_Flat
     */
    protected function _registerCoreStoreEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
            /* @var $store Mage_Core_Model_Store */
            $store = $event->getDataObject();
            $event->addNewData('contentmanager_content_flat_delete_store_id', $store->getId());
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if ($event->getType() == Blackbird_ContentManager_Model_Content_Flat_Indexer::EVENT_TYPE_REBUILD) {
            $this->_getIndexer()->getResource()->rebuild($data['id']);
            return;
        }


        if (!empty($data['contentmanager_content_flat_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['contentmanager_content_flat_content_id'])) {
            // contentmanager_content save
            $contentId = $data['contentmanager_content_flat_content_id'];
            $this->_getIndexer()->saveContent($contentId);
        } else if (!empty($data['contentmanager_content_flat_content_ids'])) {
            // contentmanager_content mass_action
            $contentIds = $data['contentmanager_content_flat_content_ids'];

            if (!empty($data['contentmanager_content_flat_website_ids'])) {
                $websiteIds = $data['contentmanager_content_flat_website_ids'];
                foreach ($websiteIds as $websiteId) {
                    $website = Mage::app()->getWebsite($websiteId);
                    foreach ($website->getStores() as $store) {
                        if ($data['contentmanager_content_flat_action_type'] == 'remove') {
                            $this->_getIndexer()->removeContent($contentIds, $store->getId());
                        } else {
                            $this->_getIndexer()->updateContent($contentIds, $store->getId());
                        }
                    }
                }
            }

            if (isset($data['contentmanager_content_flat_status'])) {
                $status = $data['contentmanager_content_flat_status'];
                $this->_getIndexer()->updateContentStatus($contentIds, $status);
            }

            if (isset($data['contentmanager_content_flat_force_update'])) {
                $this->_getIndexer()->updateContent($contentIds);
            }
        } else if (!empty($data['contentmanager_content_flat_delete_store_id'])) {
            $this->_getIndexer()->deleteStore($data['contentmanager_content_flat_delete_store_id']);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $this->_getIndexer()->reindexAll();
    }

    /**
     * Retrieve list of attribute codes, that are used in flat
     *
     * @return array
     */
    protected function _getFlatAttributes()
    {
        return Mage::getModel('contentmanager/content_flat_indexer')->getAttributeCodes();
    }
}
