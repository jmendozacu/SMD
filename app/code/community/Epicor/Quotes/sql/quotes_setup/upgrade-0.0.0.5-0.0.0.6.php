<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote_note'), 'is_private', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Private Message',
    'default' => FALSE
));

$conn->addColumn($installer->getTable('quotes/quote_note'), 'is_visible', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Visible Message',
    'default' => FALSE
));

$installer->endSetup();
