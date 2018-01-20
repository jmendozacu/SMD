<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote_note'), 'created_at', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'comment' => 'Created At'
));

$installer->endSetup();
