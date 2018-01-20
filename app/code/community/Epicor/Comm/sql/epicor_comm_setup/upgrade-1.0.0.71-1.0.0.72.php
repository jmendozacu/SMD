<?php

/**
 * Upgrade - 1.0.0.71 to 1.0.0.72
 * 
 * WSO-1231 
 *  - Add dirty column to entity register
 *  - Add is_auto to the syn log table to flag whether a syn is auto or not
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn(
    $installer->getTable('epicor_comm/entity_register'), 
    'is_dirty',
    array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'default' => false,
        'comment' => 'Flag to say if this row is dirty and needs updating'
    )
);

$conn->addColumn(
    $installer->getTable('epicor_comm/syn_log'), 
    'is_auto',
    array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'default' => false,
        'comment' => 'Flag to say if this syn was triggered from the auto syn logger'
    )
);

$conn->modifyColumn(
    $installer->getTable('epicor_comm/syn_log'), 
    'from_date', 
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'identity' => false,
        'nullable' => true,
        'primary' => false,
        'default' => null
    )
);

$installer->endSetup();