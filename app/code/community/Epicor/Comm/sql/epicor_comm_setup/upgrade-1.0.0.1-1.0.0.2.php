<?php

/**
 * Upgrade 1.0.0.0 to 1.0.0.1
 * 
 * Adds last_msq_update to products
 * 
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();

$tables = array(
    'core/store_group',
    'core/website'
);

foreach ($tables as $table) {

    $conn->addColumn($installer->getTable($table), 'company', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Company'
    ));
    $conn->addColumn($installer->getTable($table), 'site', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Site'
    ));
    $conn->addColumn($installer->getTable($table), 'warehouse', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Warehouse'
    ));
    $conn->addColumn($installer->getTable($table), 'group', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'Group'
    ));
}