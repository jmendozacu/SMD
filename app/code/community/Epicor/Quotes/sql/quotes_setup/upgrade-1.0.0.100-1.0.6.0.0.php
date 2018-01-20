<?php

/**
 * 1.0.0.100-1.0.6.0.0
 * Adding locations code to quote products
 */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote_product'), 'location_code', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Location Code'
));


$installer->endSetup();