<?php

/**
 * Upgrade - 1.0.0.69 to 1.0.0.70
 * 
 * adding details column to entity reg grid
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn(
    $installer->getTable('epicor_comm/entity_register'), 
    'details',
    array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '64k',
        'default' => null,
        'comment' => 'Entity Register Details'
    )
);

$installer->endSetup();

