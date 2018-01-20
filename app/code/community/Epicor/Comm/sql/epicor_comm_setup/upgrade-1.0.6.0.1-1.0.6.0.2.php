<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->dropTable($this->getTable('epicor_comm/location'));

$table = $conn->newTable($this->getTable('epicor_comm/location'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID'
);

$table->addColumn('erp_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Erp Code'
);

$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Name');
$table->addColumn('address1', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Address 1');
$table->addColumn('address2', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Address 2');
$table->addColumn('address3', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Address 3');
$table->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'City');
$table->addColumn('county', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'County');
$table->addColumn('county_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'County code');
$table->addColumn('country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Country');
$table->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Postcode');
$table->addColumn('telephone_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Phone');
$table->addColumn('fax_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Fax');
$table->addColumn('email_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Email');

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Created At'
);
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Updated At'
);

$conn->createTable($table);

$conn->dropTable($this->getTable('epicor_comm/location_link'));
$table = $conn->newTable($this->getTable('epicor_comm/location_link'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID'
);

$table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Linked Entity ID'
);

$table->addColumn('entity_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
    'nullable' => false,
        ), 'Linked Entity Type'
);

$table->addColumn('location_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Erp Code'
);

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Created At'
);
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Updated At'
);


$conn->createTable($table);

$conn->dropTable($this->getTable('epicor_comm/location_product'));

$table = $conn->newTable($this->getTable('epicor_comm/location_product'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID'
);

$table->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Product ID'
);

$table->addColumn('location_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Location Code'
);

$table->addColumn('stock_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Stock Status');
$table->addColumn('free_stock', Varien_Db_Ddl_Table::TYPE_VARCHAR, '16,4', array(), 'Free Stock');
$table->addColumn('minimum_order_qty', Varien_Db_Ddl_Table::TYPE_VARCHAR, '16,4', array(), 'Minimum Order Qty');
$table->addColumn('maximum_order_qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '16,4', array(), 'Maximum Order Qty');
$table->addColumn('lead_time_days', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(), 'Lead Time Days');
$table->addColumn('lead_time_text', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Lead Time Text');
$table->addColumn('supplier_brand', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Supplier Brand');
$table->addColumn('tax_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Tax Code');
$table->addColumn('manufacturers', Varien_Db_Ddl_Table::TYPE_VARCHAR, '64k', array(), 'Manufacturers');

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Created At'
);
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Updated At'
);

$conn->createTable($table);

$conn->dropTable($this->getTable('epicor_comm/location_product_currency'));

$table = $conn->newTable($this->getTable('epicor_comm/location_product_currency'));

$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID'
);

$table->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
        ), 'Product ID'
);

$table->addColumn('location_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Location Code'
);

$table->addColumn('currency_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => false,
        ), 'Currency Code'
);

$table->addColumn('cost_price', Varien_Db_Ddl_Table::TYPE_VARCHAR, '16,4', array(), 'Cost Price');
$table->addColumn('base_price', Varien_Db_Ddl_Table::TYPE_VARCHAR, '16,4', array(), 'Base Price');

$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Created At'
);
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'default' => 0
        ), 'Updated At'
);


$conn->createTable($table);

$installer->endSetup();