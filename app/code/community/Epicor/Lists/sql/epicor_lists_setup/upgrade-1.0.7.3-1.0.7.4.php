<?php

/**
 * Adding Last_used_date for contracts filteration.
 */

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();

$conn->addColumn($this->getTable('epicor_lists/contract'), 'last_used_time', array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_DATETIME,
    'comment' => 'Last Used Time'
    ));

$installer->endSetup();
