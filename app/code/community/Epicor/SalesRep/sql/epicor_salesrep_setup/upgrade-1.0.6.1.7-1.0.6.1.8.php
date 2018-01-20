<?php

/**
 * Upgrade - 1.0.6.1.7 to 1.0.6.1.8
 * 
 * Adding sales rep id column to the erp account table
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'sales_rep', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Sales Rep Id'
));


$installer->endSetup();
