<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('hostingmanager/site'), 'secure', array(
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'nullable' => false,
    'default' => 0,
    'comment' => 'All pages on your site run on https'
));

$installer->endSetup();