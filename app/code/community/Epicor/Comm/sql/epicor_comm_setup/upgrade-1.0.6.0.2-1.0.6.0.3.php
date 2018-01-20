<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tableName = $this->getTable('epicor_comm/location');

if (!$conn->tableColumnExists($tableName, 'dummy')) {
    $conn->addColumn($tableName, 'dummy', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'default' => 0,
        'comment' => 'Dummy record flag'
    ));
}

if (!$conn->tableColumnExists($tableName, 'source')) {
    $conn->addColumn($tableName, 'source', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 3,
        'default' => '',
        'comment' => 'Data Source'
    ));
}

if (!$conn->tableColumnExists($tableName, 'mobile_number')) {
    $conn->addColumn($tableName, 'mobile_number', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '15',
        'default' => '',
        'comment' => 'Data Source'
    ));
}

if ($conn->tableColumnExists($tableName, 'erp_code')) {
    $conn->changeColumn($tableName, 'erp_code', 'code', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'identity' => false,
        'nullable' => false,
        'primary' => false,
    ));
}

$tableName = $this->getTable('epicor_comm/location_link');

if (!$conn->tableColumnExists($tableName, 'link_type')) {
    $conn->addColumn($tableName, 'link_type', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'default' => '',
        'comment' => 'Link Type'
    ));
}





$installer->endSetup();
