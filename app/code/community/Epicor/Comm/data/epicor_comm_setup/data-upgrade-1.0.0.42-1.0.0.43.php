<?php

/**
 * Version 1.0.0.42 to 1.0.0.43 upgrade
 * 
 * Change all configurator products, custom option to
 * have the Title "Custom Part Reference" and not "EWA Code"
 * 
 */
$installer = $this;
$installer->startSetup();

$products = Mage::getModel('catalog/product')->getCollection();
/* @var $products Mage_Catalog_Model_Resource_Product_Collection */
$products->addAttributeToFilter('configurator', 1)
        ->addOptionsToResult();

foreach ($products->getItems() as $product) {
    foreach ($product->getOptions() as $option) {
        /* @var $option Mage_Catalog_Model_Product_Option */
        if ($option->getDefaultTitle() == 'EWA Code') {
            $option = Mage::getModel('catalog/product_option')->setStore(0)->load($option->getId());
            $option->setTitle('Custom Part Reference');
            $option->save();
        }
    }
}

$installer->endSetup();
