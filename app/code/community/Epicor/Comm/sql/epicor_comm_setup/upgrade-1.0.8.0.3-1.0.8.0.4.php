<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$config = $conn->getConfig();
// database name
$dbname = $config['dbname'];

$table = $installer->getTable('epicor_comm/location_product_currency');

//check if the product_id exist in index
$productIdQuery = "SELECT * FROM INFORMATION_SCHEMA.STATISTICS WHERE `TABLE_SCHEMA` = '" . $dbname . "' AND "
    . "`TABLE_NAME` = '" . $table . "' AND `INDEX_NAME` = 'ecc_lp_index'";

$productIdResults = $conn->fetchAll($productIdQuery);
if (count($productIdResults) == 0) {
    $conn->addIndex(
        $table, 'ecc_lp_index', array('product_id', 'location_code')
    );
}

$installer->endSetup();
