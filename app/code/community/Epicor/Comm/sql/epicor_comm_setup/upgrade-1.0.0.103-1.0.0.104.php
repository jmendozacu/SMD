<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn->addColumn($installer->getTable('sales/quote'), 'erp_account_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment' => 'ERP Account'
));

$conn->addColumn($installer->getTable('sales/order'), 'erp_account_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment' => 'ERP Account'
));

$conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_line_comment', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '64k',
    'comment' => 'ERP Account'
));

$conn->addColumn($installer->getTable('sales/order_item'), 'ecc_line_comment', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '64k',
    'comment' => 'ECC Line Comment'
));

$installer->endSetup();

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'line_comments_enabled', array(
    'group'                         => 'General',
    'label'                         => 'Enable Line Comments',
    'type'                          => 'int',
    'input'                         => 'boolean',
    'default'                       => '1',
    'required'                      => false,
    'user_defined'                  => false,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'used_in_product_listing'       => true
));

// Get the product Ids you want to affect. For me it was all downloadable products
$productIds = Mage::getResourceModel('catalog/product_collection')
        ->getAllIds();

// Now create an array of attribute_code => values
$attributeData = array('line_comments_enabled' => 1);

// Set the store to affect. I used admin to change all default values
// Now Update the attribute(s) for the given products.
Mage::getSingleton('catalog/product_action')
        ->updateAttributes($productIds, $attributeData, 0);


$installer->endSetup();