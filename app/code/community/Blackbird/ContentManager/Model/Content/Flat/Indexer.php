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


/**
 * ContentManager Content Flat Indexer Model
 *
 * @method Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer _getResource()
 * @method Blackbird_ContentManager_Model_Resource_Content_Flat_Indexer getResource()
 * @method int getEntityTypeId()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setTypeId(string $value)
 * @method string getSku()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setSku(string $value)
 * @method int getHasOptions()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Blackbird_ContentManager_Model_Content_Flat_Indexer setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Blackbird_ContentManager
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Content_Flat_Indexer extends Mage_Core_Model_Abstract
{
    /**
     * ContentManager content flat entity for indexers
     */
    const ENTITY = 'contentmanager_content_flat';

    /**
     * Indexers rebuild event type
     */
    const EVENT_TYPE_REBUILD = 'contentmanager_content_flat_rebuild';

    /**
     * Standart model resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('contentmanager/content_flat_indexer');
    }

    /**
     * Rebuild ContentManager Content Flat Data
     *
     * @param mixed $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if (is_null($store)) {
            $this->_getResource()->prepareFlatTables();
        } else {
            $this->_getResource()->prepareFlatTable($store);
        }
        Mage::getSingleton('index/indexer')->processEntityAction(
            new Varien_Object(array('id' => $store)),
            self::ENTITY,
            self::EVENT_TYPE_REBUILD
        );
        return $this;
    }

    /**
     * Update attribute data for flat table
     *
     * @param string $attributeCode
     * @param int $store
     * @param int|array $contentIds
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function updateAttribute($attributeCode, $store = null, $contentIds = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateAttribute($attributeCode, $store->getId(), $contentIds);
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $attribute = $this->_getResource()->getAttribute($attributeCode);
        $this->_getResource()->updateAttribute($attribute, $store, $contentIds);
        $this->_getResource()->updateChildrenDataFromParent($store, $contentIds);

        return $this;
    }

    /**
     * Prepare datastorage for contentmanager content flat
     *
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function prepareDataStorage($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->prepareDataStorage($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);

        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function updateEventAttributes($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateEventAttributes($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $this->_getResource()->updateEventAttributes($store);
        $this->_getResource()->updateRelationContents($store);

        return $this;
    }

    /**
     * Update content status
     *
     * @param int $contentId
     * @param int $status
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function updateContentStatus($contentId, $status, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateContentStatus($contentId, $status, $store->getId());
            }
            return $this;
        }

        if ($status == Blackbird_ContentManager_Model_Content::STATUS_ENABLED) {
            $this->_getResource()->updateContent($contentId, $store);
            $this->_getResource()->updateChildrenDataFromParent($store, $contentId);
        }
        else {
            $this->_getResource()->removeContent($contentId, $store);
        }

        return $this;
    }

    /**
     * Update ContentManager Content Flat data
     *
     * @param int|array $contentIds
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function updateContent($contentIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateContent($contentIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeContent($contentIds, $store);
            $resource->updateContent($contentIds, $store);
            $resource->updateRelationContents($store, $contentIds);
            $resource->commit();
        } catch (Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Save ContentManager Content(s) Flat data
     *
     * @param int|array $contentIds
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function saveContent($contentIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->saveContent($contentIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeContent($contentIds, $store);
            $resource->saveContent($contentIds, $store);
            $resource->updateRelationContents($store, $contentIds);
            $resource->commit();
        } catch (Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove content from flat
     *
     * @param int|array $contentIds
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function removeContent($contentIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->removeContent($contentIds, $store->getId());
            }
            return $this;
        }

        $this->_getResource()->removeContent($contentIds, $store);

        return $this;
    }

    /**
     * Delete store process
     *
     * @param int $store
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function deleteStore($store)
    {
        $this->_getResource()->deleteFlatTable($store);
        return $this;
    }

    /**
     * Rebuild ContentManager Content Flat Data for all stores
     *
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
        return $this;
    }

    /**
     * Retrieve list of attribute codes for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_getResource()->getAttributeCodes();
    }
}
