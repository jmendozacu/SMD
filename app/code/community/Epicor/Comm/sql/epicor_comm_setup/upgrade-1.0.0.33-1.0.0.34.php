<?php

/**
 * Upgrade - 1.0.0.33 to 1.0.0.34
 * 
 * Adding new column to message log for url
 */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();

$conn->addColumn($installer->getTable('epicor_comm/message_log'), 'url', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'url'
));

$installer->endSetup();
