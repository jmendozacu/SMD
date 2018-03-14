<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();


/************************************************************************
Step : Create epicor_comm_erp_attributes
*************************************************************************/

// drop tables
$conn->dropTable($this->getTable('epicor_comm/erp_mapping_attributes'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_attributes'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('attribute_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'index'   =>   true,
        ), 'Attribute Code');
$table->addColumn('input_type',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Input Type');
$table->addColumn('separator',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Separator');
$table->addColumn('use_for_config',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Use For Config');
$table->addColumn('quick_search',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Quick Search');
$table->addColumn('advanced_search',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Advanced Search');
$table->addColumn('search_weighting',Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Search Weighting');
$table->addColumn('use_in_layered_navigation',Varien_Db_Ddl_Table::TYPE_SMALLINT, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Use In Layered Navigation');
$table->addColumn('search_results',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Search Results');
$table->addColumn('visible_on_product_view',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Visible On Product View');

$conn->createTable($table); 