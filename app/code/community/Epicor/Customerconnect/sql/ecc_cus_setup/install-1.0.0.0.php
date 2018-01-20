<?php

/**
 * Customer connect install
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/* * **********************************************************************
  Step : Create ERP Mapping Invoice status Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('customerconnect/erp_mapping_invoicestatus'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_invoicestatus'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
    'nullable' => false,
        ), 'Erp Code');
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Invoice status');
$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Invoice State');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create ERP Mapping RMA status Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('customerconnect/erp_mapping_rmastatus'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_rmastatus'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
    'nullable' => false,
        ), 'Erp Code');
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'RMA status');
$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'RMA State');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create ERP Mapping Service call status Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('customerconnect/erp_mapping_servicecallstatus'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_servicecallstatus'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
    'nullable' => false,
        ), 'Erp Code');
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Service Call status');
$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Service Call State');

$conn->createTable($table);

$installer->endSetup();

