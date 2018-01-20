<?php

/**
 * Upgrade - 1.0.6.1.6 to 1.0.6.1.7
 * 
 * Adding sales rep attributes to quote & order items
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_salesrep_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Sales Rep Price'
));

$conn->addColumn($installer->getTable('sales/order_item'), 'ecc_salesrep_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Sales Rep Price'
));

$conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_salesrep_discount', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Sales Rep Discount'
));

$conn->addColumn($installer->getTable('sales/order_item'), 'ecc_salesrep_discount', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Sales Rep Discount'
));



$installer->endSetup();
