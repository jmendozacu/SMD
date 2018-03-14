<?php

/**
 * Upgrade - 1.0.7.2-1.0.7.3
 *
 * Add new attributes ship status to ERP account,
 * Column(s) to quote and order table,
 * Create new table erp_mapping_shipstatus.
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$conn = $installer->getConnection();
$tableErp = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($tableErp, 'allowed_shipstatus_methods')) {
    $conn->addColumn(
            $tableErp, 'allowed_shipstatus_methods', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '4G',
        'comment' => 'Serialized array of ship status allowed for ERP account.',
        'default' => null
            )
    );
}

if (!$conn->tableColumnExists($tableErp, 'allowed_shipstatus_methods_exclude')) {
    $conn->addColumn(
            $tableErp, 'allowed_shipstatus_methods_exclude', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '4G',
        'comment' => 'Serialized array of ship status not allowed for ERP account.',
        'default' => null
            )
    );
}
/* START of add column to sales_flat_quote and sales_flat_order table(s) */
if (!$conn->tableColumnExists($installer->getTable('sales/quote'), 'ship_status_erpcode')) {
    $installer->run("ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `ship_status_erpcode` VARCHAR(255) NOT NULL;");
}
if (!$conn->tableColumnExists($installer->getTable('sales/quote'), 'additional_reference')) {
    $installer->run("ALTER TABLE `{$installer->getTable('sales/quote')}` ADD `additional_reference` VARCHAR(255) NOT NULL;");
}
if (!$conn->tableColumnExists($installer->getTable('sales/order'), 'ship_status_erpcode')) {
    $installer->run("ALTER TABLE `{$installer->getTable('sales/order')}` ADD `ship_status_erpcode` VARCHAR(255) NOT NULL;");
}
if (!$conn->tableColumnExists($installer->getTable('sales/order'), 'additional_reference')) {
    $installer->run("ALTER TABLE `{$installer->getTable('sales/order')}` ADD `additional_reference` VARCHAR(255) NOT NULL;");
}
/* END of add column to sales_flat_quote and sales_flat_order table(s) */


$conn->dropTable($this->getTable('customerconnect/erp_mapping_shipstatus'));
$tableMapShipStatus = $conn->newTable($this->getTable('customerconnect/erp_mapping_shipstatus'));
$tableMapShipStatus->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$tableMapShipStatus->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
    'nullable' => false,
        ), 'ERP Ship status code');


$tableMapShipStatus->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
    'nullable' => false
        ), 'Ship Status Description');
/* We can use TYPE_LONGVARCHAR but this also internally converts to TEXT so defining text type only for status help field */
$tableMapShipStatus->addColumn('status_help', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
    'nullable' => false
        ), 'Ship Status Help');

$tableMapShipStatus->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'default' => 0
        ), 'Store id value');

$tableMapShipStatus->addColumn('is_default', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'default' => 1
        ), 'Default');


$conn->createTable($tableMapShipStatus);

$installer->endSetup();

