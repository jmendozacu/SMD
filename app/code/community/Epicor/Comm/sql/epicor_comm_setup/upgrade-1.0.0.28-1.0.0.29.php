<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step : Create Shipping Methods Mapping Table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/erp_mapping_shippingmethod'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_shippingmethod'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('shipping_method',Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
        'nullable'  => false,
        ), 'Shipping Method');
$table->addColumn('shipping_method_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
        'nullable'  => false,
        ), 'Shipping Method Code');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR,20, array(
        'nullable'  => false,
        ), 'Erp Code');
$conn->createTable($table); 
