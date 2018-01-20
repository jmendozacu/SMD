<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('sales/quote'), 'ecc_salesrep_customer_id')) {
    $conn->addColumn($installer->getTable('sales/quote'), 'ecc_salesrep_customer_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Sales Rep Customer ID'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/quote'), 'ecc_salesrep_chosen_customer_id')) {
    $conn->addColumn($installer->getTable('sales/quote'), 'ecc_salesrep_chosen_customer_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Sales Rep Chosen Customer ID'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order'), 'ecc_salesrep_customer_id')) {
    $conn->addColumn($installer->getTable('sales/order'), 'ecc_salesrep_customer_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Sales Rep Customer ID'
    ));
}

if (!$conn->tableColumnExists($installer->getTable('sales/order'), 'ecc_salesrep_chosen_customer_id')) {
    $conn->addColumn($installer->getTable('sales/order'), 'ecc_salesrep_chosen_customer_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Sales Rep Chosen Customer ID'
    ));
}
$installer->endSetup();
