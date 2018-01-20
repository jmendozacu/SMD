<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step : Remove quotes entry from core_resource
*************************************************************************/
$tableName = $this->getTable('core_resource');
$installer->run("DELETE FROM {$tableName} WHERE code = 'quotes_setup'");

/************************************************************************
Step : Create Erp Language Mapping Table
*************************************************************************/

// drop tables
$conn->dropTable($this->getTable('epicor_common/erp_mapping_language'));
$table=$conn->newTable($this->getTable('epicor_common/erp_mapping_language'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Erp Code');
$table->addColumn('languages',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento Local Languages');
$table->addColumn('language_codes',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5000, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento Local Language Codes');
$conn->createTable($table);