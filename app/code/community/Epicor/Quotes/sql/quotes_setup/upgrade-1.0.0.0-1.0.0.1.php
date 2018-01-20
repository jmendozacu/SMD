<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->addColumn($installer->getTable('sales/order'), 'erp_quote_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 20,
    'comment' => 'Erp Quotes Id'
));
$conn->addColumn($installer->getTable('sales/order'), 'epicor_quote_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 20,
    'comment' => 'Epicor Quotes Id'
));
$conn->addColumn($installer->getTable('sales/quote'), 'erp_quote_id', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 20,
    'comment' => 'Erp Quotes Id'
));


$installer->endSetup();