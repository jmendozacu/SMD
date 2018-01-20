<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('sales/order'), 'ecc_gqr_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 15,
    'comment' => 'Ecc Gqr Quote Number'
));
$conn->addColumn($installer->getTable('sales/order'), 'erp_gqr_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 15,
    'comment' => 'Erp Gqr Quote Number'
));
$conn->addColumn($installer->getTable('sales/quote'), 'ecc_gqr_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 15,
    'comment' => 'Ecc Gqr Quote Number'
));
$conn->addColumn($installer->getTable('sales/quote'), 'erp_gqr_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 15,
    'comment' => 'Erp Gqr Quote Number'
));

$installer->endSetup();