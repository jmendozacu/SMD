<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$table = $conn->newTable($this->getTable('hostingmanager/certificate'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Site Name');
$table->addColumn('request', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Request');
$table->addColumn('private_key', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Private Key');
$table->addColumn('certificate', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Certificate');
$table->addColumn('c_a_certificate', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'CA Certificate');

$conn->createTable($table);


$table = $conn->newTable($this->getTable('hostingmanager/site'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Site Name');
$table->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    'nullable' => false
        ), 'Site Url');
$table->addColumn('is_website', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'default' => true,
        ), 'Is Website');
$table->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Website/Store Code');
$table->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Website/Store ID');
$table->addColumn('certificate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => true,
        ), 'SSL Cert ID');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('hostingmanager/site'),
            array('child_id')
        ),
        'child_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('hostingmanager/site'),
            array('certificate_id')
        ),
        'certificate_id');

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('hostingmanager/certificate'), 
                'entity_id', 
                $this->getTable('hostingmanager/site'), 
                'certificate_id'), 
        'certificate_id', 
        $this->getTable('hostingmanager/certificate'), 'entity_id', 
        Varien_Db_Ddl_Table::ACTION_SET_NULL, 
        Varien_Db_Ddl_Table::ACTION_NO_ACTION);
$conn->createTable($table);



