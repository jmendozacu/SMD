<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($this->getTable('epicor_salesrep/account'), 'created_at', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'comment' => 'Created At'
));

$conn->addColumn($this->getTable('epicor_salesrep/account'), 'updated_at', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'comment' => 'Updated At'
));

$installer->endSetup();