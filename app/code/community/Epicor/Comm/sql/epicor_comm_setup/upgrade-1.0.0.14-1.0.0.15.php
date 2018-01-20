<?php

/**
 * Upgrade - 1.0.0.14 to 1.0.0.15
 * 
 * adding erp address county code column
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'county_code', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 32,
    'default' => null,
    'comment' => 'County code'
));

$installer->endSetup();