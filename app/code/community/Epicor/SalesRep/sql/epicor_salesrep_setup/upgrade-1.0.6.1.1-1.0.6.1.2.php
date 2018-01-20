<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->modifyColumn($this->getTable('epicor_salesrep/account'), 'sales_rep_id', 
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable'  => false,
        'comment' => 'Sales Rep Id'
    ));

$installer->endSetup();