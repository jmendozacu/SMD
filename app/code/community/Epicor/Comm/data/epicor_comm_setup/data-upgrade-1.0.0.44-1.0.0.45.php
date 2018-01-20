<?php

/**
 * Version 1.0.0.44 to 1.0.0.45 upgrade
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
    
    foreach ($product->getOptions() as $option) {
        /* @var $option Mage_Catalog_Model_Product_Option */
        if ($option->getDefaultTitle() == 'EWA Code' || $option->getDefaultTitle() == 'Custom Part Reference') {
            $option = Mage::getModel('catalog/product_option')->setStore(0)->load($option->getId());
            $option->delete();
        }
    }
    
    $options = array(
        array(
            'title' => 'Ewa Description',
            'type' => 'ewa_description',
            'is_require' => 0,
            'sort_order' => 0,
            'values' => array()
        ),
        array(
            'title' => 'Ewa Code',
            'type' => 'ewa_code',
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