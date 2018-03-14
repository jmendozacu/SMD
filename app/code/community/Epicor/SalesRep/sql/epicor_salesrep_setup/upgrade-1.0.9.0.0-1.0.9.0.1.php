<?php
$installer = new Mage_Core_Model_Resource_Setup('epicor_salesrep_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Update id of Sales Rep Pricing Rule Products Table to bigint

$tableName = $this->getTable('epicor_salesrep/pricing_rule_product');
$conn->modifyColumn($tableName, 'id', 'BIGINT(20) NOT NULL AUTO_INCREMENT');

$installer->endSetup();
