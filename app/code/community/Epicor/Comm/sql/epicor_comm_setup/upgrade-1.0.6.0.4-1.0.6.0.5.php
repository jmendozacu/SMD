<?php

// Add link type flag to ERP Acccounts
// Add link type flag to Customers

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('epicor_comm/erp_customer_group'), 'location_link_type')) {
    $conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'location_link_type', array(
        'identity' => false,
        'nullable' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'default' => 'E',
        'comment' => 'Location Link Type'
    ));
}

$installer->addAttribute('customer', 'ecc_location_link_type', array(
    'group' => 'General',
    'label' => 'Location Link Type',
    'type' => 'varchar',
    'input' => 'text',
    'default' => '',
    'visible' => false,
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'user_defined' => 0,
    'sort_order' => 5,
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'ecc_location_link_type', '999'  //sort_order
);

$installer->endSetup();
