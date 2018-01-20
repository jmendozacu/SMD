<?php

/**
 * Upgrade - 1.0.0.32 to 1.0.0.33
 * 
 * Adding new column to message log for cache type
 */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();

$conn->addColumn($installer->getTable('epicor_comm/message_log'), 'cached', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 10,
    'comment' => 'Cached'
));

$installer->endSetup();
