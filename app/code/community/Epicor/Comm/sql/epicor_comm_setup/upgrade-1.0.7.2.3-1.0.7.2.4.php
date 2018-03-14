<?php

/**
 * Upgrade - 1.0.7.2.1-1.0.7.2.2
 *
 * Add new attributes for valid payment and shipping methods.
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($table, 'allowed_delivery_methods')) {
    $conn->addColumn(
        $table, 'allowed_delivery_methods', array(
            'nullable' => true,
            'unsigned' => true,
            'primary' => false,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '4G',
            'comment' => 'Serialized array of delivery methods allowed for ERP account.',
            'default' => null
            )
        );
}

if (!$conn->tableColumnExists($table, 'allowed_delivery_methods_exclude')) {
    $conn->addColumn(
        $table, 'allowed_delivery_methods_exclude', array(
            'nullable' => true,
            'unsigned' => true,
            'primary' => false,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '4G',
            'comment' => 'Serialized array of delivery methods not allowed for ERP account.',
            'default' => null
            )
        );
}

if (!$conn->tableColumnExists($table, 'allowed_payment_methods')) {
    $conn->addColumn(
        $table, 'allowed_payment_methods', array(
            'nullable' => true,
            'unsigned' => true,
            'primary' => false,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '4G',
            'comment' => 'Serialized array of payment methods allowed for ERP account.',
            'default' => null
            )
        );
}

if (!$conn->tableColumnExists($table, 'allowed_payment_methods_exclude')) {
    $conn->addColumn(
        $table, 'allowed_payment_methods_exclude', array(
            'nullable' => true,
            'unsigned' => true,
            'primary' => false,
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => '4G',
            'comment' => 'Serialized array of payment methods not allowed for ERP account.',
            'default' => null
            )
        );
}
$installer->endSetup();

