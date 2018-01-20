<?php
/**
 * customerconnect upgrade 1.0.0.19 to 1.0.0.20
 *
 * WSO-1570 Add Return Reason Code Mapping table linkage to Erp Accounts
 *
 */
$installer = $this;
$installer->startSetup();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn = $installer->getConnection();

$conn->dropTable($this->getTable('customerconnect/erp_mapping_reasoncode_accounts'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_reasoncode_accounts'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
    'nullable' => false,
), 'Reason Code');
$table->addColumn('erp_account', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable' => false
), 'ERP Account');

$conn->createTable($table);

$installer->endSetup();

