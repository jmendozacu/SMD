<?php

/**
 * Upgrade - 1.0.0.37 to 1.0.0.38
 * 
 * Make Credit Limit Nullable
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn($this->getTable('epicor_comm/customer_erpaccount_currency'), 'credit_limit', array(
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length' => '16,4',
    'nullable' => true,
    'comment' => 'Credit Limit',
    'default' => '0.0000'
));

$installer->endSetup();