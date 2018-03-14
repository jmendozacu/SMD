<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Adding new columns to Location table
$tableName = $installer->getTable('epicor_comm/location');
if (!$conn->tableColumnExists($tableName, 'location_visible')) {
    $conn->addColumn($tableName, 'location_visible', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'default' => 1,
        'comment' => 'Location Visible'
    ));
}

if (!$conn->tableColumnExists($tableName, 'include_inventory')) {
    $conn->addColumn($tableName, 'include_inventory', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'default' => 1,
        'comment' => 'Include Inventory'
    ));
}

if (!$conn->tableColumnExists($tableName, 'show_inventory')) {
    $conn->addColumn($tableName, 'show_inventory', array(
        'identity' => false,
        'nullable' => false,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'default' => 1,
        'comment' => 'Show Inventory'
    ));
}

//Updating Locations Admin Grid Columns
$coreConfigTable = $installer->getTable('core/config_data');
$sql = 'SELECT value FROM ' . $coreConfigTable . ' WHERE path = "epicor_comm_locations/admin/grid_columns"';
$gridColumns = $conn->fetchOne($sql);
$gridColumns .= ",location_visible,include_inventory,show_inventory";
$updateSql = 'UPDATE ' . $coreConfigTable . ' SET value = "' . $gridColumns . '" WHERE path = "epicor_comm_locations/admin/grid_columns"';
$conn->query($updateSql);

// Create New Table for Related Locations
$_relatedLocationsTable = $this->getTable('epicor_comm/location_relatedlocations');
if(!$conn->isTableExists($_relatedLocationsTable)){
    $relatedlocationsTable = $conn->newTable($_relatedLocationsTable);
    $relatedlocationsTable->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID');
    $relatedlocationsTable->addColumn('location_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Location ID');
    $relatedlocationsTable->addColumn('related_location_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Related Location ID');
    $relatedlocationsTable->addForeignKey(
            $installer->getFkName($tableName,'id',$_relatedLocationsTable,'location_id'), 
            'location_id',
            $tableName, 
            'id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE);

    $conn->createTable($relatedlocationsTable);
}
// Create New Table for Groupings
$_groupingsLocationTable = $this->getTable('epicor_comm/location_groupings');
if(!$conn->isTableExists($_groupingsLocationTable)){
    $groupinglocationTable = $conn->newTable($_groupingsLocationTable);
    $groupinglocationTable->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID');
    $groupinglocationTable->addColumn('group_name',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Group Name');
    $groupinglocationTable->addColumn('group_expandable',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Group Expandable');
    $groupinglocationTable->addColumn('show_aggregate_stock',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Show Aggregate Stock');
    $groupinglocationTable->addColumn('enabled',Varien_Db_Ddl_Table::TYPE_BOOLEAN, 1, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Enabled');
    $groupinglocationTable->addColumn('order',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Order');

    $conn->createTable($groupinglocationTable);
}
//Create New Table for Locations Mapped to Group
$_groupLocationsTable = $this->getTable('epicor_comm/location_grouplocations');
if(!$conn->isTableExists($_groupLocationsTable)){
    $grouplocationsTable = $conn->newTable($_groupLocationsTable);
    $grouplocationsTable->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID');
    $grouplocationsTable->addColumn('group_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Group ID');
    $grouplocationsTable->addColumn('group_location_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Location ID');
    $grouplocationsTable->addColumn('position',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => false,
            ), 'Position');
    $grouplocationsTable->addForeignKey(
            $installer->getFkName($_groupingsLocationTable,'id',$grouplocationsTable,'group_id'), 
            'group_id',
            $_groupingsLocationTable, 
            'id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE);

    $conn->createTable($grouplocationsTable);
}
$installer->endSetup();