<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_return_line');

$conn->addColumn(
    $table, 'invoice_line',
    array(
    'nullable' => true,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Invoice Line',
    )
);


$installer->endSetup();
