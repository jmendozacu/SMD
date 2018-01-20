<?php

/**
 * Upgrade - 1.0.0.10 to 1.0.0.11
 * 
 * extending message log table xml columns
 */
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn($this->getTable('epicor_comm/message_log'), 'xml_in', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '4G',
    'nullable' => true,
    'comment' => 'Xml In'
));
$conn->modifyColumn($this->getTable('epicor_comm/message_log'), 'xml_out', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '4G',
    'nullable' => true,
    'comment' => 'Xml Out'
));

$installer->endSetup();