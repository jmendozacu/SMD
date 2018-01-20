<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn($this->getTable('epicor_comm/message_log'), 'status_code', 
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 4,
        'nullable'  => true,
        'comment' => 'Status Code'
    ));

$installer->endSetup();