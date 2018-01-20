<?php

/**
 * 1.0.6.1.4-1.0.6.1.5
 * 
 * Add index to epicor_comm_locations table
 */
$installer = $this;
$installer->startSetup();

$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/location_product');

$conn->addIndex(
        $table, $installer->getIdxName(
                $table, array('product_id', 'location_code')
        ), array('product_id', 'location_code')
);


$installer->endSetup();
