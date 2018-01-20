<?php

/**
 * Upgrade 1.0.0.90 to 1.0.0.110
 * 
 * Add store restrictions to the store table
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();

$conn->addColumn($installer->getTable('core/website'), 'allowed_customer_types', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Allowed Customer Types',
    'default' => 'all'
));

$conn->addColumn($installer->getTable('core/store_group'), 'allowed_customer_types', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Allowed Customer Types',
    'default' => null
));

$installer->endSetup();