<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$tableName = $installer->getTable('quotes/quote');

$conn->changeColumn($tableName, 'expires', 'expires', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment' => 'Expiry Date'
));

$tableName = $installer->getTable('quotes/quote_product');

$conn->changeColumn($tableName, 'orig_price', 'orig_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length' => '12,4',
    'comment' => 'Original Price'
));
$conn->changeColumn($tableName, 'new_price', 'new_price', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length' => '12,4',
    'comment' => 'New Price'
));

$installer->endSetup();
