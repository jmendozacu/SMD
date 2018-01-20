<?php

/**
 * Upgrade - 1.0.0.51 to 1.0.0.52
 * 
 * Adding custom tax attributes to quotes & orders
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Quote / Order Goods totals

$conn->addColumn($installer->getTable('sales/quote_item'), 'epicor_original_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Original Price'
));

$conn->addColumn($installer->getTable('sales/order_item'), 'epicor_original_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
    'comment' => 'Original Price'
));

$installer->endSetup();
