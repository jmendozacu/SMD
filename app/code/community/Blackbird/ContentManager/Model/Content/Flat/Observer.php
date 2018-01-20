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
 * ContentManager Content Flat observer
 *
 * @category   Mage
 * @package    Blackbird_ContentManager
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Content_Flat_Observer
{
    /**
     * Retrieve ContentManager Content Flat Helper
     *
     * @return Blackbird_ContentManager_Helper_Content_Flat
     */
    protected function _getHelper()
    {
        return Mage::helper('contentmanager/content_flat');
    }

    /**
     * Retrieve ContentManager Content Flat Indexer model
     *
     * @return Blackbird_ContentManager_Model_Content_Flat_Indexer
     */
    protected function _getIndexer() {
        return Mage::getSingleton('contentmanager/content_flat_indexer');
    }

    /**
     * ContentManager Entity attribute after save process
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerEntityAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Blackbird_ContentManager_Model_Entity_Attribute */

        $enableBefore   = ($attribute->getOrigData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getOrigData('is_filterable') > 0)
            || ($attribute->getOrigData('used_in_content_listing') == 1)
            || ($attribute->getOrigData('used_for_sort_by') == 1);
        $enableAfter    = ($attribute->getData('backend_type') == 'static')
            || ($this->_getHelper()->isAddFilterableAttributes() && $attribute->getData('is_filterable') > 0)
            || ($attribute->getData('used_in_content_listing') == 1)
            || ($attribute->getData('used_for_sort_by') == 1);

        if (!$enableAfter && !$enableBefore) {
            return $this;
        }

        if ($enableBefore && !$enableAfter) {
            // delete attribute data from flat
            $this->_getIndexer()->prepareDataStorage();
        }
        else {
            $this->_getIndexer()->updateAttribute($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * ContentManager Content Status Update
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerContentStatusUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $contentId  = $observer->getEvent()->getContentId();
        $status     = $observer->getEvent()->getStatus();
        $storeId    = $observer->getEvent()->getStoreId();
        $storeId    = $storeId > 0 ? $storeId : null;

        $this->_getIndexer()->updateContentStatus($contentId, $status, $storeId);

        return $this;
    }

    /**
     * ContentManager Content Website(s) update
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerContentWebsiteUpdate(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $contentIds = $observer->getEvent()->getContentIds();

        foreach ($websiteIds as $websiteId) {
            $website = Mage::app()->getWebsite($websiteId);
            foreach ($website->getStores() as $store) {
                if ($observer->getEvent()->getAction() == 'remove') {
                    $this->_getIndexer()->removeContent($contentIds, $store->getId());
                }
                else {
                    $this->_getIndexer()->updateContent($contentIds, $store->getId());
                }
            }
        }

        return $this;
    }

    /**
     * ContentManager Content After Save
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerContentSaveAfter(Varien_Event_Observer $observer) {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $content   = $observer->getEvent()->getContent();
        $contentId = $content->getId();

        $this->_getIndexer()->saveContent($contentId);

        return $this;
    }

    /**
     * Add new store flat process
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function storeAdd(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        $this->_getIndexer()->rebuild($store->getId());

        return $this;
    }

    /**
     * Store edit action, check change store group
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function storeEdit(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */
        if ($store->dataHasChangedFor('group_id')) {
            $this->_getIndexer()->rebuild($store->getId());
        }

        return $this;
    }

    /**
     * Store delete after process
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function storeDelete(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */

        $this->_getIndexer()->deleteStore($store->getId());

        return $this;
    }

    /**
     * Store Group Save process
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function storeGroupSave(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $group = $observer->getEvent()->getGroup();
        /* @var $group Mage_Core_Model_Store_Group */

        if ($group->dataHasChangedFor('website_id')) {
            foreach ($group->getStores() as $store) {
                /* @var $store Mage_Core_Model_Store */
                $this->_getIndexer()->rebuild($store->getId());
            }
        }

        return $this;
    }

    /**
     * ContentManager Content Import After process
     *
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerContentImportAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $this->_getIndexer()->rebuild();

        return $this;
    }

    /**
     * Customer Group save after process
     *
     * @param Varien_Event_Observer_Collection $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function customerGroupSaveAfter(Varien_Event_Observer $observer)
    {
        if (!$this->_getHelper()->isAvailable() || !$this->_getHelper()->isBuilt()) {
            return $this;
        }

        $customerGroup = $observer->getEvent()->getObject();
        /* @var $customerGroup Mage_Customer_Model_Group */
        if ($customerGroup->dataHasChangedFor($customerGroup->getIdFieldName())
            || $customerGroup->dataHasChangedFor('tax_class_id')) {
            $this->_getIndexer()->updateEventAttributes();
        }
        return $this;
    }

    /**
     * Update category ids in flat
     *
     * @deprecated 1.3.2.2
     * @param Varien_Event_Observer $observer
     * @return Blackbird_ContentManager_Model_Content_Flat_Observer
     */
    public function contentmanagerCategoryChangeContents(Varien_Event_Observer $observer)
    {
        return $this;
    }
}
