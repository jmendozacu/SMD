<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_return');

$conn->addColumn(
    $table, 'submitted',
    array(
    'nullable' => false,
    'length' => 1,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'comment' => 'Submitted',
    'default' => 0
    )
);

$config = Mage::getConfig()->init();
/* @var $config Mage_Core_Model_Config */

$erp = $config->getStoresConfigByPath('Epicor_Comm/licensing/erp');

if (is_array($erp)) {
    $erp = array_pop($erp);
}

if ($erp == 'p21' || $erp == 'e10') {
    $config->saveConfig('epicor_comm_returns/guests/find_lines_by', 'order_number,serial_number');
    $config->saveConfig('epicor_comm_returns/b2c/find_lines_by', 'order_number,serial_number');
}

Mage::app()->cleanCache(array('CONFIG'));


$installer->endSetup();
