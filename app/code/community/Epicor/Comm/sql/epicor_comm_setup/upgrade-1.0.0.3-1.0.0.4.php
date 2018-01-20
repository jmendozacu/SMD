<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();


/* * **********************************************************************
  Step : Create Erp Currency Mapping Table
 * *********************************************************************** */

// drop tables
$conn->dropTable($this->getTable('epicor_comm/customer_erpaccount_currency'));
$table = $conn->newTable($this->getTable('epicor_comm/customer_erpaccount_currency'));
/* @var $table Varien_Db_Ddl_Table */
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('erp_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Erp Code');
$table->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => '0'
        ), 'Default Currency');
$table->addColumn('currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
    'nullable' => false,
        ), 'Currency Code');
$table->addColumn('onstop', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => '0'
        ), 'On Stop');
$table->addColumn('balance', Varien_Db_Ddl_Table::TYPE_DECIMAL, '16,4', array(
    'nullable' => false,
    'default' => '0.0000'
        ), 'Balance');
$table->addColumn('credit_limit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '16,4', array(
    'nullable' => false,
    'default' => '0.0000'
        ), 'Credit Limit');
$table->addColumn('unallocated_cash', Varien_Db_Ddl_Table::TYPE_DECIMAL, '16,4', array(
    'nullable' => false,
    'default' => '0.0000'
        ), 'Unallocated Cash');
$table->addColumn('min_order_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '16,4', array(
    'default' => '0.0000'
        ), 'Minimum Order Amount');
$table->addColumn('last_payment_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Last Payment Date');
$table->addColumn('last_payment_value', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
        ), 'Last Payment Value');
$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
        ), 'Created At');
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
        ), 'Updated At');

$table->addIndex($this->getTable('epicor_comm/customer_erpaccount_currency') . '_erp_account_id_idx', 'erp_account_id');

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', $this->getTable('epicor_comm/customer_erpaccount_currency'), 'erp_account_id'), 'erp_account_id', $this->getTable('epicor_comm/erp_customer_group'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);
