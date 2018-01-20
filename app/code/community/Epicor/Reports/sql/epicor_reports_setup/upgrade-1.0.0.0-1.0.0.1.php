<?php
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('epicor_reports/rawdata'), 'cached', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 10,
    'comment' => 'Cached'
));

$installer->endSetup();