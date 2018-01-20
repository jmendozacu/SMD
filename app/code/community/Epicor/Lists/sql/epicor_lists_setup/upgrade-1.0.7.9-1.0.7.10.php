<?php

$installer = $this;

$installer->startSetup();
$tableName = $this->getTable('epicor_lists/list_address_restriction');



$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
if ($conn->isTableExists($installer->getTable('epicor_lists/list_address_restriction'))) {
    return;
}

$table = $conn->newTable($tableName);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$foreignTableListAddress = $this->getTable('epicor_lists/list_address');

$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);

$table->addColumn(
    'restriction_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
    'nullable' => false
    ), 'Restriction Type'
);

$table->addColumn(
    'address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'Address Id'
);

$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);
$table->addForeignKey(
    $installer->getFkName(
        $foreignTableListAddress, 'id', $tableName, 'address_id'
    ), 'address_id', $foreignTableListAddress, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);


$conn->createTable($table);

$installer->endSetup();
?>