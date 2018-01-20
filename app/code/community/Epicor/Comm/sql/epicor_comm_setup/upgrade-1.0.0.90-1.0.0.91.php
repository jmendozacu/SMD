<?php

/**
 * Upgrade - 1.0.0.90 to 1.0.0.91
 * 
 * Renaming customerCode to addressCode in returns
 */
$installer = $this;
$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$table = $this->getTable('epicor_comm/customer_return');
if ($conn->tableColumnExists($table, 'customer_code')) {
    $conn->changeColumn(
        $table, 'customer_code', 'address_code',
        array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'identity' => false,
        'nullable' => true,
        'primary' => false,
        )
    );
}    

$installer->endSetup();
