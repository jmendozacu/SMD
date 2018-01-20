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
        
    $options = array(
        array(
            'title' => 'Ewa Title',
            'type' => 'ewa_title',
            'is_require' => 0,
            'sort_order' => 0,
            'values' => array()
        ),
        array(
            'title' => 'Ewa Short Description',
            'type' => 'ewa_short_description',
            'is_require' => 0,
            'sort_order' => 0,
            'values' => array()
        ),
        array(
            'title' => 'Ewa SKU',
            'type' => 'ewa_sku',
            'is_require' => 0,
            'sort_order' => 0,
            'values' => array()
        )
    );

    if (!$product->getOptionsReadonly()) {
        Mage::getSingleton('catalog/product_option')->unsetOptions();
        $product->setProductOptions($options);
        $product->setCanSaveCustomOptions(true);
    }

    $product->save();
}

$installer->endSetup();