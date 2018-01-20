<?php

/**
 * customerconnect upgrade 1.0.0.12 to 1.0.0.13
 * 
 *  - Add ERP Quote status mapping table
 * 
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/* * **********************************************************************
  Step : Create ERP Mapping Quote status Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('customerconnect/erp_mapping_erpquotestatus'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_erpquotestatus'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
    'nullable' => false,
        ), 'Erp Quote Code');
$table->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Erp Quote status');
$table->addColumn('state', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
    'nullable' => false,
    'default' => 'open'
        ), 'Erp Quote State');

$conn->createTable($table);

$installer->endSetup();

