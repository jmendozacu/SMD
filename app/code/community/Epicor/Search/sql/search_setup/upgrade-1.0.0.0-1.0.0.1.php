<?php

/**
 * Upgrade - 1.0.0.0 to 1.0.0.1
 * 
 * adding type to access groups
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('catalog/eav_attribute'), 'weighting', array(
    'nullable' => true,
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'length' => 10,
    'default' => 1,
    'comment' => 'Search Weighting'
));


$installer->endSetup();
