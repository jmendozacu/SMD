<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


$address_model = Mage::getModel('epicor_comm/customer_erpaccount_address');
/* @var $address_model Epicor_Comm_Model_Customer_Erpaccount_Address */

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'type', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 50,
    'default' => $address_model::ADDRESS_DEFAULT,
    'comment' => 'Address Type'
));
$installer->endSetup();