<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_return');

$conn->modifyColumn(
    $table, 'store_id',
    array(
    'nullable' => false,
    'unsigned' => true,
    'length' => 12,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment' => 'Store ID',
    'default' => 0
    )
);

$installer->endSetup();
