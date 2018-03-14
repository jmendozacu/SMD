<?php

/**
 * Version 1.0.0.46 to 1.0.0.47 upgrade
 * 
 * Change all configurator products to have 2 new option types: "EWA Code" and "EWA Description"
 * 
 */
$installer = $this;
$installer->startSetup();

$products = Mage::getModel('catalog/product')->getCollection();
/* @var $products Mage_Catalog_Model_Resource_Product_Collection */
$products->addAttributeToFilter('configurator', 1)
        ->addOptionsToResult();

foreach ($products->getItems() as $product) {
    
    $optionInstance = $product->getOptionInstance();

    $optionInstance->addOption(array(
        'title' => 'Ewa Configurable',
        'type' => 'ewa_configurable',
        'is_require' => 0,
        'sort_order' => 0,
        'values' => array()
    ));

    $optionInstance->setProduct($product);
    $optionInstance->saveOptions();
    $product->getOptionInstance()->unsetOptions();
}

$installer->endSetup();