<?php

/**
 * Upgrade - 1.0.0.1 to 1.0.0.2
 * 
 * adding attribute variable used_in_search_ordering
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('catalog/eav_attribute'), 'used_in_search_ordering', array(
    'nullable' => true,
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'default' => 0,
    'comment' => 'Used in search ordering'
));


$installer->endSetup();
