<?php

/**
 * 1.0.6.6.1-1.0.6.6.2
 *
 * Add indexes to locations tables
 */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/location');

$conn->addIndex(
    $table, $installer->getIdxName(
        $table, array('code')
    ), array('code')
);

$table = $installer->getTable('epicor_comm/location_product_currency');

$conn->addIndex(
    $table, $installer->getIdxName(
        $table, array('location_code', 'currency_code')
    ), array('location_code', 'currency_code')
);

$installer->endSetup();
