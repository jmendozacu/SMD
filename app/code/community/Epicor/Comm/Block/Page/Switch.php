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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store and language switcher block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Epicor_Comm_Block_Page_Switch extends Mage_Page_Block_Switch
{

    public function getRawStores()
    {
        if (!$this->hasData('raw_stores')) {

            parent::getRawStores();

            //$storeIds = Mage::helper('epicor_comm')->getCustomerStores($customer);
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccount = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

            $stores = $this->getData('raw_stores');
            $tempStores = array();
            foreach ($stores as $groupId => $groupStores) {
                foreach ($groupStores as $store) {
                    if (!$erpAccount || $erpAccount->isValidForStore($store)) {
                        $tempStores[$groupId][$store->getId()] = $store;
                    }
                }
            }

            $this->setData('raw_stores', $tempStores);
        }

        return $this->getData('raw_stores');
    }

    public function getGroups()
    {
        if ($this->getIsStoreSelector()) {
            if (!$this->hasData('groups')) {
                $rawGroups = $this->getRawGroups();
                $rawStores = $this->getRawStores();

                $groups = array();
                $localeCode = Mage::getStoreConfig('general/locale/code');
                foreach ($rawGroups as $group) {
                    /* @var $group Mage_Core_Model_Store_Group */
                    if (!isset($rawStores[$group->getId()])) {
                        continue;
                    }
                    if ($group->getId() == $this->getCurrentGroupId()) {
                        $groups[] = $group;
                        continue;
                    }

                    $store = $group->getDefaultStoreByLocale($localeCode);
                    $store->setHomeUrl(Mage::getUrl('epicor_comm/store/select', array('code' => $store->getCode())));

                    if ($store) {
                        $group->setHomeUrl($store->getHomeUrl());
                        $groups[] = $group;
                    }
                }
                $this->setData('groups', $groups);
            }
        }
        return parent::getGroups();
    }

}
