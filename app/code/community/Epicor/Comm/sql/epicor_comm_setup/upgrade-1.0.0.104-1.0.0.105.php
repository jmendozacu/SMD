<?php

/**
 * Upgrade - 1.0.0.104 to 1.0.0.105
 * 
 * Adding warranty customer setting to erp accounts
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'is_warranty_customer', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => false,
    'comment' => 'Flag to say if this erp account needs is a warranty customer'
));

$installer->endSetup();
