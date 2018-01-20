<?php

/**
 * Upgrade - 1.0.0.106 to 1.0.0.107
 * 
 * Adding dates to items
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Quote / Order Item Date Required value
$conn->addColumn($installer->getTable('sales/quote'), 'delivery_type', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 25,
    'comment' => 'Delivery type, full / partial',
    'default' => ''
));

$conn->addColumn($installer->getTable('sales/order'), 'delivery_type', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 25,
    'comment' => 'Delivery type, full / partial',
    'default' => ''
));

$conn->addColumn($installer->getTable('sales/quote_item'), 'delivery_deferred', array(
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Deferred delivery',
    'default' => 0
));

$conn->addColumn($installer->getTable('sales/order_item'), 'delivery_deferred', array(
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Deferred delivery',
    'default' => 0
));

$installer->endSetup();
