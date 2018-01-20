<?php

/**
 * Upgrade - 1.0.0.55 to 1.0.0.56
 * 
 * extending message log table secondary subject column
 */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn($this->getTable('epicor_comm/message_log'), 'message_secondary_subject', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '64k',
));

$installer->endSetup();
