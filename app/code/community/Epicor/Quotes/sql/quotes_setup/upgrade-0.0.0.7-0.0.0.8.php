<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote'), 'quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 100,
    'comment' => 'ERP Quote Number'
));

$conn->addColumn($installer->getTable('quotes/quote_note'), 'erp_ref', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 100,
    'comment' => 'ERP Reference'
));

$conn->addColumn($installer->getTable('quotes/quote_product'), 'erp_note_ref', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 100,
    'comment' => 'ERP Reference'
));


$installer->endSetup();