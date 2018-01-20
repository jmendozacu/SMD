<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('quotes/quote'), 'send_comments', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Comment Notification Emails',
    'default' => TRUE
));

$installer->endSetup();
