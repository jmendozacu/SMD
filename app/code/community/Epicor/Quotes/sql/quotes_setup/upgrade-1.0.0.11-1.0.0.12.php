<?php

/**
 * 1.0.0.11 - 1.0.0.12 - adding quote erp line number
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn(
    $installer->getTable('quotes/quote_product'), 'erp_line_number',
    array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'ERP Line Number',
        'default' => ''
    )
);

$installer->endSetup();
