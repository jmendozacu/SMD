<?php
/*
 *  upgrade script to add column to shipment item table
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/*Create an ERP customer account attribute tax_exempt_reference*/
$table = $installer->getTable('sales/shipment_item');

if (!$conn->tableColumnExists($table, 'ecc_erp_shipment_number')) {
    $conn->addColumn($table, 'ecc_erp_shipment_number', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 11,
        'comment' => 'ECC ERP Shipment Number'
    ));
}

$installer->endSetup();
