<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step : Update length of column payment_collected in epicor_comm_erp_mapping_payment 
*************************************************************************/
$table = $installer->getTable('epicor_comm/erp_mapping_payment');

$conn->modifyColumn($table, 'payment_collected', 'VARCHAR(1)');

$installer->endSetup();