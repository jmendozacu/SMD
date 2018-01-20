<?php

/**
 * Upgrade - 1.0.0.66 to 1.0.0.67
 * 
 * adding entity register and syn log tables
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/* * **********************************************************************
  Create Entity Register table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_comm/entity_register'));
$table = $conn->newTable($this->getTable('epicor_comm/entity_register'));
$table->addColumn('row_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Row ID');

$table->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Type');

$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Group ID');

$table->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Group ID');

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Created At');

$table->addColumn('modified_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Modified At');

$table->addColumn('manually_modified_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Modified At');

$table->addColumn('to_be_deleted', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'To Be deleted flag'); 

$conn->createTable($table);

/* * **********************************************************************
  Create SYN Log Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_comm/syn_log'));
$table = $conn->newTable($this->getTable('epicor_comm/syn_log'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Row ID');

$table->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Message');

$table->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'From Date');


$table->addColumn('types', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Types');

$table->addColumn('brands', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Brands');

$table->addColumn('languages', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Languages');

$table->addColumn('created_by_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Created By');

$table->addColumn('created_by_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Created By Name');

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Created At');

$conn->createTable($table);

$installer->endSetup();

