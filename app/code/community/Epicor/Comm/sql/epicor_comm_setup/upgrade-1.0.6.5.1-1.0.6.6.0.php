<?php

/**
 * Upgrade - 1.0.6.5.1-1.0.6.6.0
 *
 * Add new setting to make PO# mandatory
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($table, 'po_mandatory')) {
    $conn->addColumn(
            $table, 'po_mandatory', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Purchase Order Mandatory, NULL: Default, 0: Disabled, 1: Enabled',
        'default' => null
            )
    );
}

$installer->endSetup();

