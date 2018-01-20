<?php
/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
/**
 * Creating table epicor_faqs
 */
//Raw data table
$conn->dropTable($this->getTable('epicor_reports/rawdata'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('epicor_reports/rawdata'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned' => true,
		'identity' => true,
		'nullable' => false,
		'primary' => true,
	), 'Entity id')
	->addColumn('store', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
		'unsigned' => true,
		'nullable' => false,
	), 'Store id')
    ->addColumn('message_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'nullable' => true,
        'default' => null,
    ), 'Message type')
    ->addColumn('message_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
        'nullable' => true,
        'default' => null,
    ), 'Message status')
    ->addColumn('duration', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => false,
        'nullable' => false,
        'default' => 0
    ), 'Duration')
    ->addColumn('time', Varien_Db_Ddl_Table::TYPE_DATETIME  , null, array(
        'nullable' => false,
    ), 'Message Time')
    ->addColumn('messaging_log_id', Varien_Db_Ddl_Table::TYPE_INTEGER  , null, array(
        'unsigned' => false,
        'nullable' => true,
    ), 'Epicor Comm Messaging Log Id')
    ->setComment('Raw data item');

$installer->getConnection()->createTable($table);
$installer->endSetup();
