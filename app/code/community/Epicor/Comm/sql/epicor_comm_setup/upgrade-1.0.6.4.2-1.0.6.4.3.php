<?php

/**
 * Upgrade - 1.0.6.4.2.1.0.6.4.3
 *
 * Setting Enabled CPN Editing to Disabled
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("DELETE FROM " . $this->getTable('core/config_data') . " WHERE path LIKE '%epicor_comm_field_mapping/cpn_mapping/customer_part_enable_editing%'");

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('epicor_comm/erp_customer_group');

if ($conn->tableColumnExists($table, 'cpn_editing')) {
    $installer->run("UPDATE " . $table . " SET cpn_editing = NULL WHERE cpn_editing IS NOT NULL AND entity_id > 0");
}

$installer->endSetup();