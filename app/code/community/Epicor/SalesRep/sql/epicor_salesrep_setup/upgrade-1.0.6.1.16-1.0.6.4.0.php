<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('sales/quote_item'), 'ecc_salesrep_rule_price')) {
    $conn->addColumn($installer->getTable('sales/quote_item'), 'ecc_salesrep_rule_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sales Rep Rule Price'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order_item'), 'ecc_salesrep_rule_price')) {
    $conn->addColumn($installer->getTable('sales/order_item'), 'ecc_salesrep_rule_price', array(
        'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
        'comment' => 'Sales Rep Rule Price'
    ));
}

$installer->endSetup();
