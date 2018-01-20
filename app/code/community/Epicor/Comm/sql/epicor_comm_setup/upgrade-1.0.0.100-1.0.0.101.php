<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();


/************************************************************************
Step : Create Erp Remote Links Mapping Table
*************************************************************************/

// drop tables
$conn->dropTable($this->getTable('epicor_comm/erp_mapping_remotelinks'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_remotelinks'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('pattern_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => true,
        ), 'Pattern Code');
$table->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Name');
$table->addColumn('url_pattern',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Url Pattern');
$table->addColumn('http_authorization',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Http Authorization');
$table->addColumn('auth_user',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Auth User');
$table->addColumn('auth_password',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Auth Password');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/erp_mapping_remotelinks'), 
            array('pattern_code'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('pattern_code'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table); 