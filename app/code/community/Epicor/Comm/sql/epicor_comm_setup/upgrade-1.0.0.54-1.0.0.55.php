<?php

/**
 * Upgrade - 1.0.0.53 to 1.0.0.54
 * 
 * Adding custom tax attributes to quotes & orders
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->dropTable($this->getTable('epicor_comm/message_queue'));
$table=$conn->newTable($this->getTable('epicor_comm/message_queue'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('message_id',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'primary'   => true,
        ), 'Message Id');

$table->addColumn('message_category',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Message Category');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_DECIMAL, '14,4', array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento ID');
$conn->createTable($table); 
$installer->endSetup();
