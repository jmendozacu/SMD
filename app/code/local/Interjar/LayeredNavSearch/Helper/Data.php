<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 16:24
 */
class Interjar_LayeredNavSearch_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Constants for System Configuration Paths
     */
    const ENABLED_CONFIG_PATH = 'layerednavsearch/general/enabled';
    const SEARCHABLE_ATTRIBUTES_CONFIG_PATH = 'layerednavsearch/general/searchable_attributes';

    /**
     * Return Value from Config for whether search is enabled
     *
     * @param null $storeId
     * @return mixed
     */
    public function getSearchEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::ENABLED_CONFIG_PATH, $storeId);
    }

    /**
     * Return Value from Config for whether search is enabled
     *
     * @param null $storeId
     * @return mixed
     */
    public function getSearchableAttributes($storeId = null)
    {
        return Mage::getStoreConfig(self::SEARCHABLE_ATTRIBUTES_CONFIG_PATH, $storeId);
    }
}
