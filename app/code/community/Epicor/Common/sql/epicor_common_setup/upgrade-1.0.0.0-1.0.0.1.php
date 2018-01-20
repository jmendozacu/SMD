<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// remove old access tables

$installer->run('DROP TABLE IF EXISTS `epicor_comm_access_right_section`');
$installer->run('DROP TABLE IF EXISTS `epicor_comm_access_group_right`');
$installer->run('DROP TABLE IF EXISTS `epicor_comm_access_group_customer`');
$installer->run('DROP TABLE IF EXISTS `epicor_comm_access_right`');
$installer->run('DROP TABLE IF EXISTS `epicor_comm_access_group`');
$installer->run('DROP TABLE IF EXISTS `epicor_comm_controller_list`');

/* * **********************************************************************
  Step : Create Access Right Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_right'));
$table = $conn->newTable($this->getTable('epicor_common/access_right'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('entity_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Erp Code');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create Access Group Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_group'));
$table = $conn->newTable($this->getTable('epicor_common/access_group'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('entity_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Entity Name');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create Access Group Right Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_group_right'));
$table = $conn->newTable($this->getTable('epicor_common/access_group_right'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('right_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Right ID');
$table->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Group ID');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create Access Rights element Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_right_element'));
$table = $conn->newTable($this->getTable('epicor_common/access_right_element'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');

$table->addColumn('right_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Right ID');

$table->addColumn('element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Element ID');

$conn->createTable($table);

/* * **********************************************************************
  Step : Create Access Element List Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_element'));
$controllerTable = $conn->newTable($this->getTable('epicor_common/access_element'));

$controllerTable->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');

$controllerTable->addColumn('module', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Module');

$controllerTable->addColumn('controller', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Controller');

$controllerTable->addColumn('action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Action');

$controllerTable->addColumn('block', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Block');

$controllerTable->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
        ), 'Action Type ');

$controllerTable->addColumn('excluded', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'default' => 0
        ), 'Excluded ');

$conn->createTable($controllerTable);

/* * **********************************************************************
  Step : Create Access Group Customer Table
 * *********************************************************************** */

$conn->dropTable($this->getTable('epicor_common/access_group_customer'));
$table = $conn->newTable($this->getTable('epicor_common/access_group_customer'));
$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Group ID');
$table->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'nullable' => false,
    'unsigned' => true,
        ), 'Customer ID');

$conn->createTable($table);

/************************************************************************
Step : Add Foreign Keys to Access Right Tables
*************************************************************************/

$conn->addForeignKey(
    $installer->getFkName('epicor_common/access_right', 'entity_id', 'epicor_common/access_right_element', 'right_id'),
    $installer->getTable('epicor_common/access_right_element'),
    'right_id',
    $installer->getTable('epicor_common/access_right'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->addForeignKey(
    $installer->getFkName('epicor_common/access_group', 'entity_id', 'epicor_common/access_group_customer', 'group_id'),
    $installer->getTable('epicor_common/access_group_customer'),
    'group_id',
    $installer->getTable('epicor_common/access_group'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->addForeignKey(
    $installer->getFkName('customer/entity', 'entity_id', 'epicor_common/access_group_customer', 'customer_id'),
    $installer->getTable('epicor_common/access_group_customer'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->addForeignKey(
    $installer->getFkName('epicor_common/access_right', 'entity_id', 'epicor_common/access_group_right', 'right_id'),
    $installer->getTable('epicor_common/access_group_right'),
    'right_id',
    $installer->getTable('epicor_common/access_right'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->addForeignKey(
    $installer->getFkName('epicor_common/access_group', 'entity_id', 'epicor_common/access_group_right', 'group_id'),
    $installer->getTable('epicor_common/access_group_right'),
    'group_id',
    $installer->getTable('epicor_common/access_group'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->removeAttribute('customer', 'access_groups');
$installer->endSetup();

