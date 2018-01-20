<?php
/**
 * Upgrade - 1.0.0.17 to 1.0.0.18
 *
 * WSO-1570 Create return reason code mapping table
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->addColumn($installer->getTable('customerconnect/erp_mapping_reasoncode'),
    'type',
    array(
        'nullable' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'Not present or blank – all types, B – B2B sites only, C – B2C sites only'
    )
);