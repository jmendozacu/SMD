<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('hostingmanager/certificate'), 'issue_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'default' => 0,
    'comment' => 'Certificate Issue Number'
));

$installer->endSetup();