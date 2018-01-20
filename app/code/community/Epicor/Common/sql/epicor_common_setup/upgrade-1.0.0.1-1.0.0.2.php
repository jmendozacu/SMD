<?php

/**
 * Upgrade - 1.0.0.1 to 1.0.0.2
 * 
 * adding access right group erp account
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_common/access_group'), 'erp_account_id', array(
    'nullable' => true,
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'default' => null,
    'comment' => 'ERP Account ID'
));

$conn->addForeignKey(
    $installer->getFkName('epicor_comm/erp_customer_group', 'entity_id','epicor_common/access_group', 'erp_account_id'),
    $installer->getTable('epicor_common/access_group'),
    'erp_account_id',
    $installer->getTable('epicor_comm/erp_customer_group'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->endSetup();