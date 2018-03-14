<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
/************************************************************************
Step : Update length of column ECC ERP Shipment Number in epicor_comm_erp_mapping_payment 
*************************************************************************/
$table = $installer->getTable('sales/shipment_item');
$conn->modifyColumn($table, 'ecc_erp_shipment_number','VARCHAR(255)');
$installer->endSetup();