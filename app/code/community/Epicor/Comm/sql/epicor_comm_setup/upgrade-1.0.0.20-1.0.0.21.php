<?php

/**
 * Upgrade - 1.0.0.20 to 1.0.0.21
 * 
 * Creating Store link tables for ERP Customer groups and addresses
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


/* * **********************************************************************
  Step : Create Customer Erp Groups
 * *********************************************************************** */


$conn->dropTable($this->getTable('epicor_comm/erp_customer_group_store'));

$table = $conn->newTable($this->getTable('epicor_comm/erp_customer_group_store'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('erp_customer_group', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Erp Group ID');
$table->addColumn('store', Varien_Db_Ddl_Table::TYPE_SMALLINT, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Store ID');

$table->addIndex($this->getTable('epicor_comm/erp_customer_group_store') . '_erp_customer_group_id_idx', 'erp_customer_group');
$table->addIndex($this->getTable('epicor_comm/erp_customer_group_store') . '_store_idx', 'store');

$table->addIndex($this->getTable('epicor_comm/customer_erpaccount_address') . '_erp_unique_idx', array(
    'erp_customer_group',
    'store',
));

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/erp_customer_group'), 'erp_customer_group', $this->getTable('epicor_comm/erp_customer_group_store'), 'entity_id'), 'erp_customer_group', $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);



/* * **********************************************************************
  Step : Create Customer Erp Addresses
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_comm/customer_erpaccount_address_store'));

$table = $conn->newTable($this->getTable('epicor_comm/customer_erpaccount_address_store'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('erp_customer_group_address', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Erp Account Code');
$table->addColumn('store', Varien_Db_Ddl_Table::TYPE_SMALLINT, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Store ID');

$table->addIndex($this->getTable('epicor_comm/customer_erpaccount_address_store') . '_erp_unique_idx', array(
    'erp_customer_group_address',
    'store'
));

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/customer_erpaccount_address'), 'entity_id', $this->getTable('epicor_comm/customer_erpaccount_address_store'), 'erp_customer_group_address'), 'erp_customer_group_address', $this->getTable('epicor_comm/customer_erpaccount_address'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);

