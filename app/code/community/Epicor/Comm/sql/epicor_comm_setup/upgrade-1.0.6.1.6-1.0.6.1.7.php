<?php
/**
 * 1.0.6.1.6-1.0.6.1.7
 *
 * Add index to epicor_comm_locations table
 */

$installer = $this;
$installer->startSetup();

$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/customer_sku');

$conn->addIndex(
        $table, $installer->getIdxName(
                $table, array('product_id', 'customer_group_id', 'sku')
        ), array('product_id', 'customer_group_id', 'sku')
);


$installer->endSetup();
