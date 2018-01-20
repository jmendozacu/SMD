<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_return');

$conn->addColumn(
    $table, 'store_id',
    array(
    'nullable' => false,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Store ID',
    'default' => ''
    )
);

$conn->addColumn(
    $table, 'erp_sync_action',
    array(
    'nullable' => false,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'ERP Sync Action',
    'default' => ''
    )
);

$conn->addColumn(
    $table, 'erp_sync_status',
    array(
    'nullable' => false,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'ERP Sync Status',
    'default' => 'N'
    )
);

$conn->addColumn(
    $table, 'last_erp_status',
    array(
    'nullable' => false,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'ERP Sync Last Status',
    'default' => ''
    )
);

$conn->addColumn(
    $table, 'last_erp_error_description',
    array(
    'nullable' => false,
    'length' => 255,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'ERP Sync Last Description',
    'default' => ''
    )
);

$installer->endSetup();
