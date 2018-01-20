<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();


/************************************************************************
Step : Create Erp Card Type Mapping Table
*************************************************************************/

// drop tables
$conn->dropTable($this->getTable('epicor_comm/erp_mapping_cardtype'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_cardtype'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('card_description',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Card Description');
$table->addColumn('magento_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento Code');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Erp Code');
$conn->createTable($table); 