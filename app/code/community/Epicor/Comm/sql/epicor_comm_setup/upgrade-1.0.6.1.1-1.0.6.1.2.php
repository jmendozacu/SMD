<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_comm/location'),'sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER);


