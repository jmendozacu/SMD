<?php
/**
 * customerconnect upgrade 1.0.0.16 to 1.0.0.17
 *
 * WSO-1570 Add Return Reason Code Mapping table
 *
 */
$installer = $this;
$installer->startSetup();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn = $installer->getConnection();

$conn->dropTable($this->getTable('customerconnect/erp_mapping_reasoncode'));
$table = $conn->newTable($this->getTable('customerconnect/erp_mapping_reasoncode'));
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
), 'ID');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
    'nullable' => false,
), 'Reason Code');
$table->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
    'nullable' => false
), 'Reason Code Description');
$table->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'default' => 0
), 'Store id value');

$conn->createTable($table);

$installer->endSetup();

