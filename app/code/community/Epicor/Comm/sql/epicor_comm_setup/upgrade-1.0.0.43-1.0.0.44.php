<?php

/**
 * Upgrade - 1.0.0.43 to 1.0.0.44
 * 
 * Add sou trigger to order status table
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($this->getTable('epicor_comm/erp_mapping_orderstatus'), 'sou_trigger', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '50',
    'nullable' => true,
    'comment' => 'Sou Trigger',
    'default' => null
)); 

$installer->endSetup();