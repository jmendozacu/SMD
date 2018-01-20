<?php

/**
 * Upgrade - 1.0.0.8 to 1.0.0.9
 *
 * WSO-1300 Add store_id to mapping tables
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn(
    $installer->getTable('epicor_common/erp_mapping_language'),
    'store_id',
    array(
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'default' => 0,
        'comment' => 'Store id value'
    )
);

$installer->endSetup();
