<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */

try{
    
    $helper->createLayoutBlock(
        'Category Layered Navigation', 
        'catalog/layer_view', 
        'catalog/layer/view.phtml', 
        NULL, NULL, NULL, NULL, 
        'catalog.leftnav', 
        NULL
    );
} catch (Exception $ex) {
    Mage::log('Could not create block Category Layered Navigation, block already exists');
}

$installer->endSetup();
