<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote'), 'show_prices', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Show Prices',
    'default' => FALSE
));

$installer->endSetup();
