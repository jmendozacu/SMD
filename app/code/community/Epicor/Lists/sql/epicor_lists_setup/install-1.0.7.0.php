<?php

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * LISTS
 */
// Create Lists Table
$tableName = $this->getTable('epicor_lists/list');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);

$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'List ECC ID'
);

$table->addColumn(
    'erp_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false
    ), 'List Erp Code'
);

$table->addColumn(
    'type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false
    ), 'List Type'
);

$table->addColumn(
    'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false
    ), 'List Title'
);

$table->addColumn(
    'label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Label for List'
);
$table->addColumn(
    'settings', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
    'nullable' => false
    ), 'List Status Flags'
);
$table->addColumn(
    'start_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'Start Date'
);
$table->addColumn(
    'end_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'End Date'
);
$table->addColumn(
    'active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => 0
    ), 'Active'
);
$table->addColumn(
    'source', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
    'nullable' => false,
    'default' => 'web'
    ), 'Source (Customer / Web / ERP)'
);
$table->addColumn(
    'default_currency', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(
    'nullable' => true
    ), 'Default Currency Code'
);
$table->addColumn(
    'conditions', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => true
    ), 'Product Filter Conditions'
);
$table->addColumn(
    'erp_account_link_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => false,
    'default' => 'N'
    ), 'How are ERP Accounts linked?'
);
$table->addColumn(
    'is_dummy', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => 0
    ), 'Was this created by a sub message? (e.g. CUS)'
);
$table->addColumn(
    'notes', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => true
    ), 'Notes Field'
);
$table->addColumn(
    'erp_override', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => true
    ), 'Serialized array of data the ERP cannot override'
);
$table->addColumn(
    'priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => true,
    'default' => 0
    ), 'Priority'
);
$table->addColumn(
    'created_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => false,
    ), 'Start Date'
);
$table->addColumn(
    'updated_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => false,
    ), 'End Date'
);
$table->addColumn(
    'description', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => true
    ), 'Description'
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('active', 'start_date', 'end_date'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ), array('active', 'start_date', 'end_date'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('type'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ), array('type'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('erp_account_link_type'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ), array('erp_account_link_type'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$conn->createTable($table);


// Create List Labels Table

$tableName = $this->getTable('epicor_lists/list_label');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);

$table->addColumn(
    'website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => true
    ), 'Website ID'
);

$table->addColumn(
    'store_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => true
    ), 'Store Group ID'
);

$table->addColumn(
    'store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => true
    ), 'Store ID'
);
$table->addColumn(
    'label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Store Specific Label'
);

$foreignKeyTableName = $this->getTable('core/website');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'website_id', $tableName, 'website_id'
    ), 'website_id', $foreignKeyTableName, 'website_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('core/store_group');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'group_id', $tableName, 'store_group_id'
    ), 'store_group_id', $foreignKeyTableName, 'group_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);



$foreignKeyTableName = $this->getTable('core/store');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'store_id', $tableName, 'store_id'
    ), 'store_id', $foreignKeyTableName, 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);


// Create List Brands Table

$tableName = $this->getTable('epicor_lists/list_brand');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);

$table->addColumn(
    'company', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Company'
);

$table->addColumn(
    'site', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Site'
);

$table->addColumn(
    'warehouse', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Warehouse'
);

$table->addColumn(
    'group', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Group'
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->createTable($table);

// Create List Website Table

$tableName = $this->getTable('epicor_lists/list_website');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'Website ID'
);


$foreignKeyTableName = $this->getTable('core/website');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'website_id', $tableName, 'website_id'
    ), 'website_id', $foreignKeyTableName, 'website_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'website_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'website_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);

// Create List Store Group Table

$tableName = $this->getTable('epicor_lists/list_store_group');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'store_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'Store ID'
);


$foreignKeyTableName = $this->getTable('core/store_group');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'group_id', $tableName, 'store_group_id'
    ), 'store_group_id', $foreignKeyTableName, 'group_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'store_group_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'store_group_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);



// Create List Erp Account Table

$tableName = $this->getTable('epicor_lists/list_erp_account');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'erp_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'ERP Account ID'
);


$foreignKeyTableName = $this->getTable('epicor_comm/customer_erpaccount');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'entity_id', $tableName, 'erp_account_id'
    ), 'erp_account_id', $foreignKeyTableName, 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'erp_account_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'erp_account_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);



// Create List Customer Table

$tableName = $this->getTable('epicor_lists/list_customer');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'Customer ID'
);


$foreignKeyTableName = $this->getTable('customer/entity');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'entity_id', $tableName, 'customer_id'
    ), 'customer_id', $foreignKeyTableName, 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'customer_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'customer_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);



// Create List Product Table

$tableName = $this->getTable('epicor_lists/list_product');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);

$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);

$table->addColumn(
    'sku', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
    'nullable' => false
    ), 'Product SKU'
);

$table->addColumn(
    'qty', Varien_Db_Ddl_Table::TYPE_FLOAT, '12,4', array(
    'nullable' => true
    ), 'Product Qty'
);


$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_id', 'sku'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('list_id', 'sku'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

$conn->createTable($table);



// Create List Product Price Table

$tableName = $this->getTable('epicor_lists/list_product_price');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List Product ID'
);
$table->addColumn(
    'currency', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(
    'nullable' => false
    ), 'Currency Code'
);
$table->addColumn(
    'price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
    'nullable' => false
    ), 'Product Price for this Currency'
);
$table->addColumn(
    'price_breaks', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(), 'Serialized array of Price Breaks'
);
$table->addColumn(
    'value_breaks', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(), 'Serialized array of Value Breaks'
);

$foreignKeyTableName = $this->getTable('epicor_lists/list_product');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_product_id'
    ), 'list_product_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('list_product_id')
    ), array('list_product_id')
);

$conn->createTable($table);



// Create List Addess Table

$tableName = $this->getTable('epicor_lists/list_address');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'address_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'ERP Address Code'
);
$table->addColumn(
    'purchase_order_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Address PO Humber'
);
$table->addColumn(
    'name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Address Name'
);
$table->addColumn(
    'address1', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Line 1'
);
$table->addColumn(
    'address2', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Line 2'
);
$table->addColumn(
    'address3', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Line 3'
);
$table->addColumn(
    'city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'City'
);
$table->addColumn(
    'county', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'County'
);
$table->addColumn(
    'country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Country'
);
$table->addColumn(
    'postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Postcode'
);
$table->addColumn(
    'telephone_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Telephone Number'
);
$table->addColumn(
    'mobile_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Mobile Number'
);
$table->addColumn(
    'fax_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Fax Number'
);
$table->addColumn(
    'email_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Email Address'
);

$table->addColumn(
    'activation_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'List Address Activation Date'
);

$table->addColumn(
    'expiry_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'List Address Expiry Date'
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('activation_date', 'expiry_date', 'address_code', 'list_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ), array('activation_date', 'expiry_date', 'address_code', 'list_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
);

$conn->createTable($table);

/**
 * Contracts New Tables & Columns
 */
// Create Contract Table

$tableName = $this->getTable('epicor_lists/contract');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);
$table->addColumn(
    'list_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List ID'
);
$table->addColumn(
    'sales_rep', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Sales Rep'
);
$table->addColumn(
    'contact_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'Contact Name'
);
$table->addColumn(
    'purchase_order_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true
    ), 'PO Humber'
);
$table->addColumn(
    'last_modified_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    'default' =>'0000-00-00 00:00:00'
    ), 'Last Modified Date'
);
$table->addColumn(
    'contract_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => true
    ), 'Contract Status'
);

$foreignKeyTableName = $this->getTable('epicor_lists/list');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_id'
    ), 'list_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->createTable($table);


// Create List Product Price Table

$tableName = $this->getTable('epicor_lists/contract_product');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true
    ), 'Incremental ID'
);

$table->addColumn(
    'contract_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'Contract ID'
);

$table->addColumn(
    'list_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false
    ), 'List Product ID'
);

$table->addColumn(
    'line_number', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => true
    ), 'Contract Line Number'
);

$table->addColumn(
    'part_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
    'nullable' => true
    ), 'Contract Part Number'
);

$table->addColumn(
    'status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => true
    ), 'Contract Line Status'
);

$table->addColumn(
    'start_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'Contract Line Start Date'
);
$table->addColumn(
    'end_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    'nullable' => true,
    ), 'Contract Line End Date'
);

$table->addColumn(
    'min_order_qty', Varien_Db_Ddl_Table::TYPE_FLOAT, '12,4', array(
    'nullable' => true
    ), 'Minimum Order Qty'
);

$table->addColumn(
    'max_order_qty', Varien_Db_Ddl_Table::TYPE_FLOAT, '12,4', array(
    'nullable' => true
    ), 'Maximum Order Qty'
);

$table->addColumn(
    'is_discountable', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => true
    ), 'Contract Product Is Discountable'
);

$foreignKeyTableName = $this->getTable('epicor_lists/contract');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'contract_id'
    ), 'contract_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);


$foreignKeyTableName = $this->getTable('epicor_lists/list_product');
$table->addForeignKey(
    $installer->getFkName(
        $foreignKeyTableName, 'id', $tableName, 'list_product_id'
    ), 'list_product_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addIndex(
    $installer->getIdxName(
        $tableName, array('contract_id', 'list_product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ), array('contract_id', 'list_product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);
$conn->createTable($table);

$installer->endSetup();

// Add Columns to ERP Account Table
$tableName = $installer->getTable('epicor_comm/customer_erpaccount');

$columns = array(
    'allowed_contract_type' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'Allowed Contract Type'
    ),
    'required_contract_type' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'Required Contract Type'
    ),
    'allow_non_contract_items' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Allow Non Contract Items'
    ),
    'contract_shipto_default' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 50,
        'comment' => 'Default Ship to Selection'
    ),
    'contract_shipto_date' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 10,
        'comment' => 'Use Ship To Based on Contract Date'
    ),
    'contract_shipto_prompt' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Prompt for Ship To Selection if More Than 1'
    ),
    'contract_header_selection' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 10,
        'comment' => 'Header Contract Selection'
    ),
    'contract_header_prompt' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Prompt for Header Selection if More Than 1'
    ),
    'contract_header_always' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Always use Header Contract when Available'
    ),
    'contract_line_selection' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 10,
        'comment' => 'Line Contract Selection'
    ),
    'contract_line_prompt' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Show Dropdown for Optional Contracts'
    ),
    'contract_line_always' => array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Always use Line Level Contract when Available'
    ),
);

foreach ($columns as $name => $definition) {
    $installHelper->addTableColumn($conn, $tableName, $name, $definition);
}

// ADD Quote / Order / GQR columns

$columnName = 'ecc_contract_code';
$columnDefinition = array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'ECC Contract Code'
);

$installHelper->addTableColumn($conn, $installer->getTable('sales/quote'), $columnName, $columnDefinition);
$installHelper->addTableColumn($conn, $installer->getTable('sales/quote_item'), $columnName, $columnDefinition);

// Add new order columns

$installHelper->addTableColumn($conn, $installer->getTable('sales/order'), $columnName, $columnDefinition);
$installHelper->addTableColumn($conn, $installer->getTable('sales/order_item'), $columnName, $columnDefinition);

// Add new attributes to customer

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$attributes = array(
    'ecc_master_shopper' => array(
        'group' => 'General',
        'label' => 'Master Shopper',
        'type' => 'int',
        'input' => 'boolean',
        'default' => 0,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contracts_filter' => array(
        'group' => 'General',
        'label' => 'Contract Filter',
        'type' => 'varchar',
        'input' => 'text',
        'default' => null,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_default_contract' => array(
        'group' => 'General',
        'label' => 'Default Contract',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_default_contract_address' => array(
        'group' => 'General',
        'label' => 'Default Contract Address',
        'type' => 'varchar',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_shipto_default' => array(
        'group' => 'General',
        'label' => 'Default Ship To Selection',
        'type' => 'varchar',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_shipto_date' => array(
        'group' => 'General',
        'label' => 'Use Ship To Based on Contract Date',
        'type' => 'varchar',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_shipto_prompt' => array(
        'group' => 'General',
        'label' => 'Prompt for Ship To Selection if More Than 1',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_header_selection' => array(
        'group' => 'General',
        'label' => 'Header Contract Selection',
        'type' => 'varchar',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_header_prompt' => array(
        'group' => 'General',
        'label' => 'Prompt for Header Selection if More Than 1',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_header_always' => array(
        'group' => 'General',
        'label' => 'Always use Header Contract when Available',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_line_selection' => array(
        'group' => 'General',
        'label' => 'Line Contract Selection',
        'type' => 'varchar',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_line_prompt' => array(
        'group' => 'General',
        'label' => 'Show Dropdown for Optional Contracts',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    ),
    'ecc_contract_line_always' => array(
        'group' => 'General',
        'label' => 'Always use Line Level Contract when Available',
        'type' => 'int',
        'default' => null,
        'visible' => false,
        'required' => false,
        'user_defined' => false,
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'visible_in_advanced_search' => false,
    )
);



foreach ($attributes as $name => $definition) {
    $installHelper->addCustomerAttribute($installer, $name, $definition);
}


//update all customers to be master shopper if customer and b2b and not if customer and b2c
$customers = Mage::getModel('customer/customer')->getCollection()
    ->addAttributeToSelect('ecc_erp_account_type')
    ->addAttributeToSelect('customer_erp_account')
    ->addAttributeToSelect('erpaccount_id');
foreach ($customers as $customer) {
    if ($customer->getData('ecc_erp_account_type') == 'customer') {
        $erpAccount = Mage::getModel('epicor_comm/customer_erpaccount')->load($customer->getErpaccountId(), 'entity_id');
        if ($erpAccount->isTypeB2b()) {
            $customer->setEccMasterShopper(true);
        } else {
            $customer->setEccMasterShopper(false);
        }
        $customer->setIsMasterShopperIsFormated(true);
        $customer->getResource()->saveAttribute($customer, 'ecc_master_shopper');
    }
}


//update attribute

$installer->updateAttribute('customer', 'ecc_default_contract', 'frontend_model', 'epicor_common/eav_entity_attribute_frontend_erpdefaultcontract');
$installer->updateAttribute('customer', 'ecc_default_contract_address', 'frontend_model', 'epicor_common/eav_entity_attribute_frontend_erpdefaultcontractaddress');
$installer->updateAttribute('customer', 'ecc_contracts_filter', 'frontend_model', 'epicor_common/eav_entity_attribute_frontend_erpcontractfilter');

// ACCESS RIGHTS

// create new access right 
$listIndexRight = $installHelper->addAccessRight('Customerconnect - Lists - Contracts - Index');    
$listDetailsRight = $installHelper->addAccessRight('Customerconnect - Lists - Contracts - Details');  


// create new access right elements
$lists = $installHelper->addAccessElement('Epicor_Lists', '*', '*', '', 'Access');
$index = $installHelper->addAccessElement('Epicor_Lists', 'Contracts', 'index', '', 'Access');
$details = $installHelper->addAccessElement('Customerconnect', 'Lists', 'details', '', 'Access');

//add elements to new access rights
$installHelper->addAccessRightElementById($listIndexRight->getid(),$index->getId());
$installHelper->addAccessRightElementById($listDetailsRight->getid(),$details->getId());

// load existing 'customer full access' access right
$fullAccess = $installHelper->loadAccessRightByName('Customer - Full Access');

// add new element to customer full access
$installHelper->addAccessRightElementById($fullAccess->getid(),$lists->getId());

// load existing groups to add to
$readWriteGroup = $installHelper->loadAccessGroupByName('Customerconnect - Full Access');
$readOnlyGroup = $installHelper->loadAccessGroupByName('Customerconnect - Read Only'); 

// add access right to existing access group
$installHelper->addAccessGroupRight($readWriteGroup->getId(), $listIndexRight->getId());
$installHelper->addAccessGroupRight($readWriteGroup->getId(), $listDetailsRight->getId());
$installHelper->addAccessGroupRight($readOnlyGroup->getId(), $listIndexRight->getId());
$installHelper->addAccessGroupRight($readOnlyGroup->getId(), $listDetailsRight->getId());



$installer->endSetup();
