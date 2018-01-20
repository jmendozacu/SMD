<?php

/**
 * Upgrade - 1.0.0.6 to 1.0.0.7
 * 
 * adding type to access groups
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_common/access_group'), 'type', array(
    'nullable' => true,
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 30,
    'default' => 'customer',
    'comment' => 'Access Group Type'
));

$conn->addColumn($installer->getTable('epicor_common/access_right'), 'type', array(
    'nullable' => true,
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 30,
    'default' => 'customer',
    'comment' => 'Access Right Type'
));


$installer->endSetup();
