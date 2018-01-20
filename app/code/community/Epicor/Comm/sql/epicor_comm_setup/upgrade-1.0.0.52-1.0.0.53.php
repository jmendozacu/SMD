<?php

/**
 * Upgrade - 1.0.0.52 to 1.0.0.53
 * 
 * Adding erp short code eg "ADDISON" instead of "2"
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'short_code', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Erp Account Short Code'
));
$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'account_number', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Erp Account Number'
));
$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'company', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'comment' => 'Erp Account Company'
));

$installer->endSetup();
