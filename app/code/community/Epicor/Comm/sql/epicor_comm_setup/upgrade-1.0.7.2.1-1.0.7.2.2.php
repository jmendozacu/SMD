<?php

/**
 * 1.0.7.2.0-1.0.7.2.1
 *
 * Adds column created_by to eav_attributes_set and eav_attributes table
 */
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('eav/attribute_set'), 'created_by')) {
    $conn->addColumn($installer->getTable('eav/attribute_set'), 'created_by', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Created By'
    ));
}
if (!$conn->tableColumnExists($installer->getTable('eav/attribute'), 'created_by')) {
    $conn->addColumn($installer->getTable('eav/attribute'), 'created_by', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Created By'
    ));
}

$installer->endSetup();