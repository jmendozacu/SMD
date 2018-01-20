<?php

/**
 * 1.0.0.14 - 1.0.0.15 
 * 
 * Flexitheme upgrade to make it re-generate active layouts so that previous upgrades to 
 * block ids don't screw with active layouts
 */
$installer = $this;
$installer->startSetup();

$config = Mage::getConfig();
$packages = $config->getStoresConfigByPath('design/package/name');
$layouts = $config->getStoresConfigByPath('design/theme/layout');

$layoutModels = Mage::getModel('flexitheme/layout')->getCollection();
/* @var $layoutModels Epicor_FlexiTheme_Model_Mysql4_Layout_Collection */

$helper = Mage::helper('flexitheme/layout');
/* @var $helper Epicor_FlexiTheme_Helper_Layout */

foreach ($layoutModels as $layout) {
    /* @var $layout Epicor_FlexiTheme_Model_Layout */

    $layoutData = unserialize(base64_decode($helper->exportLayout($layout)));

    $layout->buildXml();

    foreach (array_keys($packages) as $storeId) {
        if (
            $packages[$storeId] == 'flexitheme' && $layouts[$storeId] == $helper->safeString($layout->getLayoutName())
        ) {
            $helper->setActiveLayout($layout->getId(), $storeId);
        }
    }
}

Mage::app()->cleanCache(array('LAYOUT_GENERAL_CACHE_TAG', 'BLOCK_HTML'));

$installer->endSetup();
