<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote_product'), 'options', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '4G',
    'comment' => 'Product Options'
));

$installer->endSetup();