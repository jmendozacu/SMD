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

$conn->dropTable($this->getTable('customerconnect/erp_mapping_erporderstatus'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_erporderstatus'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
    'nullable' => false,
        ), 'Erp Order Code');
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Erp Order status');
$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Erp Order State');

$conn->createTable($table);

$installer->endSetup();

