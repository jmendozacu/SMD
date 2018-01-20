<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('hostingmanager/site'), 'is_default', array(
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => 0,
    'comment' => 'Default Website Scope'
));

$installer->endSetup();