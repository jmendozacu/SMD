<?php

/**
 * 1.0.0.12 - 1.0.0.13 - adding quote store id
 */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn(
    $installer->getTable('quotes/quote'), 'store_id',
    array(
        'nullable' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 12,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        'comment' => 'Store ID',
    )
);

$installer->endSetup();
