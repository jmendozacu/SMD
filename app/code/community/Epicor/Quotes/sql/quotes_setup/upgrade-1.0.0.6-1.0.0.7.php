<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * Step 1: Add ERP Quote Number column to quote table
 */

$table = $installer->getTable('quotes/quote');

$conn->addColumn(
    $table, 
    'quote_number',
    array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 100,
        'comment' => 'ERP QUOTE ID',
    )
);

$conn->addIndex(
        $this->getTable('quotes/quote'),
        $installer->getIdxName(
            $this->getTable('quotes/quote'), 
            array('quote_number')
        ),
        'quote_number'
);

$installer->endSetup();
