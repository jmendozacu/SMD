<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $installer->getTable('quotes/quote');

$conn->changeColumn($table, 'send_reminder', 'send_customer_reminders', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Reminder Emails - Customer',
    'default' => TRUE
));
$conn->addColumn($table, 'send_admin_reminders', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Reminder Emails - Admin',
    'default' => TRUE
));

$conn->changeColumn($table, 'send_comments', 'send_customer_comments', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Comment Emails - Customer',
    'default' => TRUE
));

$conn->addColumn($table, 'send_admin_comments', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Comment Emails - Admin',
    'default' => TRUE
));

$conn->addColumn($table, 'send_customer_updates', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Update Emails - Customer',
    'default' => TRUE
));

$conn->addColumn($table, 'send_admin_updates', array(
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'comment' => 'Send Update Emails - Admin',
    'default' => TRUE
));

$installer->endSetup();
