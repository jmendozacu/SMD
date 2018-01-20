<?php

/**
 * Adding dates to addresses
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tableName = $this->getTable('epicor_lists/list_address');

if (!$conn->tableColumnExists($tableName, 'activation_date')) {
    $conn->addColumn($tableName, 'activation_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'List Address Activation Date'
    ));

    $conn->addIndex(
        $tableName, $installer->getIdxName(
            $tableName, array('activation_date'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ), array('activation_date'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    );
}

if (!$conn->tableColumnExists($tableName, 'expiry_date')) {
    $conn->addColumn($tableName, 'expiry_date', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'comment' => 'List Address Expiry Date'
    ));
}

$installer->endSetup();
