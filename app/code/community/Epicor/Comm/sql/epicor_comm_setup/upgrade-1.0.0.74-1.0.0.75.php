<?php

/**
 * Upgrade - 1.0.0.72 to 1.0.0.73
 * 
 * fixing magento_code on ard type mapping table
 */


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn(
    $this->getTable('epicor_comm/erp_mapping_cardtype'), 
    'magento_code', 
    array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    )
);


$installer->endSetup();
