<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


/************************************************************************
Step : Create ERP Catalog Category Table
*************************************************************************/

// Create erp_catalog_category
$conn->dropTable($this->getTable('epicor_comm/erp_catalog_category'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_catalog_category'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Erp Code');
$table->addColumn('magento_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento ID');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'default'   =>0
        ), 'Created At');

$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'default'   =>0
        ), 'Updated At');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/erp_catalog_category'),
            array('erp_code')
        ),
        'erp_code');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/erp_catalog_category'),
            array('magento_id')
        ),'magento_id');
$table->addForeignKey(
        $installer->getFkName($this->getTable('catalog/category'),'entity_id',$this->getTable('epicor_comm/erp_catalog_category'),'magento_id'), 
        'magento_id',
        $this->getTable('catalog/category'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);

/************************************************************************
Step : Create Customer Erp Groups
*************************************************************************/


// drop tables
$conn->dropTable($this->getTable('epicor_comm/erp_customer_group'));

$table=$conn->newTable($this->getTable('epicor_comm/erp_customer_group'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Erp Code');
$table->addColumn('magento_id',Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento ID');
$table->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Name');
$table->addColumn('allow_backorders',Varien_Db_Ddl_Table::TYPE_TINYINT,1, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Allow Backorders');
$table->addColumn('allow_cash_on_delivery',Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Allow Cash on Delivery');
$table->addColumn('onstop',Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'On Stop');
$table->addColumn('balance',Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Balance');
$table->addColumn('credit_limit',Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Credit Limit');
$table->addColumn('unallocated_cash',Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Unallocated Cash');
$table->addColumn('currency_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 8, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Currency Code');
$table->addColumn('last_payment_date',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'default'   =>0
        ), 'Last Payment Date');
$table->addColumn('last_payment_value',Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Last Payment Value');
$table->addColumn('email',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Email');
$table->addColumn('default_payment_method_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Default Payment Method Code');
$table->addColumn('default_delivery_address_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Default Delivery Address Code');
$table->addColumn('default_delivery_method_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Default Delivery Method Code');
$table->addColumn('default_invoice_address_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Default Invoice Address Code');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        'default'   =>0
        ), 'Created At');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        'default'   =>0
        ), 'Updated At');
$table->addColumn('min_order_amount',Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'default'   => 0.00
        ), 'Minimum Order Amount');

$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/erp_customer_group'),
            array('erp_code')
        ),
        'erp_code');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/erp_customer_group'),
            array('magento_id')
        ),
        'magento_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('customer/customer_group'),
                'customer_group_id',
                $this->getTable('epicor_comm/erp_customer_group'),
                'magento_id'), 
        'magento_id',
        $this->getTable('customer/customer_group'), 
        'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);



/************************************************************************
Step : Create Customer Erp Addresses
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/customer_erpaccount_address'));

$table=$conn->newTable($this->getTable('epicor_comm/customer_erpaccount_address'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Erp Code');
$table->addColumn('erp_customer_group_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Erp Customer Group Code');
$table->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Name');
$table->addColumn('address1',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Address 1');
$table->addColumn('address2',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Address 2');
$table->addColumn('address3',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Address 3');
$table->addColumn('city',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'City');
$table->addColumn('county',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'County');
$table->addColumn('country',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Country');
$table->addColumn('postcode',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Postcode');
$table->addColumn('phone',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Phone');
$table->addColumn('fax',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Fax');
$table->addColumn('email',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Email');
$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   =>0
        ), 'Created At');
$table->addColumn('updated_at',Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   =>0
        ), 'Updated At');
$table->addColumn('instructions',Varien_Db_Ddl_Table::TYPE_TEXT, 5000, array(
        'default'   => ''
        ), 'Instructions');




$table->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_comm/customer_erpaccount_address'),
            array('erp_code', 'erp_customer_group_code')
        ),
        array('erp_code', 'erp_customer_group_code')
        );

$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('epicor_comm/erp_customer_group'),
                'erp_code',
                $this->getTable('epicor_comm/customer_erpaccount_address'),
                'erp_customer_group_code'), 
        'erp_customer_group_code',
        $this->getTable('epicor_comm/erp_customer_group'), 
        'erp_code',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);


/************************************************************************
Step : Add Attributes to Customer Addresses
*************************************************************************/

$addressHelper = Mage::helper('customer/address');
$store         = Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
 
/* @var $eavConfig Mage_Eav_Model_Config */
$eavConfig = Mage::getSingleton('eav/config');
 
// update customer address user defined attributes data
$attributes = array(
    'erp_group_code' => array(   
        'frontend_label'    => 'ERP Group Code',
        'backend_type'     => 'varchar',
        'frontend_input'    => 'text',
        'is_user_defined'   => 1,
        'is_system'         => 0,
        'is_visible'        => 1,
        'sort_order'        => 140,
        'is_required'       => 0,
        'multiline_count'   => 0,
        'validate_rules'    => array(
            'max_text_length'   => 255,
            'min_text_length'   => 1
        ),
    ),
);
 
foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
	$usedInForms = array(
	    'adminhtml_customer_address',
	    'customer_address_edit',
	    'customer_register_address'
	);
	$attribute->setData('used_in_forms', $usedInForms);
    $attribute->save();
}
// update customer address user defined attributes data
$attributes = array(
    'erp_address_code' => array(   
        'frontend_label'    => 'ERP Address Code',
        'backend_type'     => 'varchar',
        'frontend_input'    => 'text',
        'is_user_defined'   => 1,
        'is_system'         => 0,
        'is_visible'        => 1,
        'sort_order'        => 140,
        'is_required'       => 0,
        'multiline_count'   => 0,
        'validate_rules'    => array(
            'max_text_length'   => 255,
            'min_text_length'   => 1
        ),
    ),
);
 
foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
	$usedInForms = array(
	    'adminhtml_customer_address',
	    'customer_address_edit',
	    'customer_register_address'
	);
	$attribute->setData('used_in_forms', $usedInForms);
    $attribute->save();
}

 
// update customer address user defined attributes data
$attributes = array(
    'instructions' => array(   
        'frontend_label'    => 'Instructions',
        'backend_type'     => 'varchar',
        'frontend_input'    => 'textarea',
        'is_user_defined'   => 1,
        'is_system'         => 0,
        'is_visible'        => 1,
        'sort_order'        => 140,
        'is_required'       => 0,
        'multiline_count'   => 0,
        'validate_rules'    => array(
            'max_text_length'   => 5000,
            'min_text_length'   => 0
        ),
    ),
);
 
foreach ($attributes as $attributeCode => $data) {
    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
    $attribute->setWebsite($store->getWebsite());
    $attribute->addData($data);
	$usedInForms = array(
	    'adminhtml_customer_address',
	    'customer_address_edit',
	    'customer_register_address'
	);
	$attribute->setData('used_in_forms', $usedInForms);
    $attribute->save();
}
//
//$attributes = array(
//    'erp_delivery_address_code' => array(   
//        'frontend_label'    => 'ERP Delivery Address Code',
//        'backend_type'     => 'varchar',
//        'frontend_input'    => 'text',
//        'is_user_defined'   => 1,
//        'is_system'         => 0,
//        'is_visible'        => 1,
//        'sort_order'        => 140,
//        'is_required'       => 0,
//        'multiline_count'   => 0,
//        'validate_rules'    => array(
//            'max_text_length'   => 255,
//            'min_text_length'   => 1
//        ),
//    ),
//);
// 
//foreach ($attributes as $attributeCode => $data) {
//    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
//    $attribute->setWebsite($store->getWebsite());
//    $attribute->addData($data);
//	$usedInForms = array(
//	    'adminhtml_customer_address',
//	    'customer_address_edit',
//	    'customer_register_address'
//	);
//	$attribute->setData('used_in_forms', $usedInForms);
//    $attribute->save();
//}
//
//$attributes = array(
//    'erp_invoice_address_code' => array(   
//        'frontend_label'    => 'ERP Invoice Address Code',
//        'backend_type'     => 'varchar',
//        'frontend_input'    => 'text',
//        'is_user_defined'   => 1,
//        'is_system'         => 0,
//        'is_visible'        => 1,
//        'sort_order'        => 141,
//        'is_required'       => 0,
//        'multiline_count'   => 0,
//        'validate_rules'    => array(
//            'max_text_length'   => 255,
//            'min_text_length'   => 1
//        ),
//    ),
//);
// 
//foreach ($attributes as $attributeCode => $data) {
//    $attribute = $eavConfig->getAttribute('customer_address', $attributeCode);
//    $attribute->setWebsite($store->getWebsite());
//    $attribute->addData($data);
//	$usedInForms = array(
//	    'adminhtml_customer_address',
//	    'customer_address_edit',
//	    'customer_register_address'
//	);
//	$attribute->setData('used_in_forms', $usedInForms);
//    $attribute->save();
//}

/************************************************************************
Step : Add Columns to Flat Orders & Quote Addresses
*************************************************************************/

//$conn->addColumn($this->getTable('sales_flat_quote_address'), 
//        'erp_delivery_address_code', 
//        array(
//        'identity'  => false,
//        'nullable'  => true,
//        'primary'   => false,
//        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
//        'length'    => 255,
//        'comment'=> 'Erp delivery address code'
//        ));
//
//$conn->addColumn($this->getTable('sales_flat_order_address'), 
//        'erp_delivery_address_code', 
//        array(
//        'identity'  => false,
//        'nullable'  => true,
//        'primary'   => false,
//        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
//        'length'    => 255,
//        'comment'=> 'Erp delivery address code'
//        ));

//$conn->addColumn($this->getTable('sales_flat_quote_address'), 
//        'erp_invoice_address_code', 
//        array(
//        'identity'  => false,
//        'nullable'  => true,
//        'primary'   => false,
//        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
//        'length'    => 255,
//        'comment'   => 'Erp Invoice address code'
//        ));
//
//$conn->addColumn($this->getTable('sales_flat_order_address'), 
//        'erp_invoice_address_code', 
//        array(
//        'identity'  => false,
//        'nullable'  => true,
//        'primary'   => false,
//        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
//        'length'    => 255,
//        'comment'   => 'Erp invoice address code'
//        ));

/************************************************************************
Step : Add Columns to Sales Orders & Quotes
*************************************************************************/

/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->addColumn($installer->getTable('sales/order'), 
        'gor_sent',
        array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length'    => 1,
        'default'   => 0,
        'comment'   => 'Gor Sent'
        ));

$conn->addColumn($installer->getTable('sales/order'), 
        'gor_message',
        array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'default'   =>'Order Not Sent',
        'comment'   => 'Gor Message'
        ));
$conn->addColumn($installer->getTable('sales/order'), 
        'erp_order_number', 
         array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'ERP Order Number'
        ));

$conn->addColumn($installer->getTable('sales/order'), 'last_sod_update', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_DATE,
    'comment' => 'Last update from ERP'
));


$conn->addColumn($installer->getTable('sales/order'), 
        'required_date',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable'  => false,
            'default' => '0000-00-00 00:00:00',
            'comment' => 'Require Date',
        ));

$conn->addColumn($installer->getTable('sales/quote'), 
        'required_date',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable'  => false,
            'default' => '0000-00-00 00:00:00',
            'comment' => 'Require Date',
        ));

$conn->addColumn($installer->getTable('sales/order'), 'initial_grand_total', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'comment' => 'Initial Grand Total',
        'scale'     => 4,
        'precision' => 12,
    ));

$conn->addColumn($installer->getTable('sales/order'), 'device_used', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Device Used',
        'length' => 255
    ));

/************************************************************************
Step : Create XML Message Log Table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/message_log'));
$table=$conn->newTable($this->getTable('epicor_comm/message_log'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');

$table->addColumn('message_parent',Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Message Parent');

$table->addColumn('message_category',Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Message Category');

$table->addColumn('message_type',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Message Type');

$table->addColumn('message_subject',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Message Subject');

$table->addColumn('message_secondary_subject',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Message Secondary Subject');

$table->addColumn('start_datestamp',Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Start Datestamp');

$table->addColumn('end_datestamp',Varien_Db_Ddl_Table::TYPE_DATETIME, 255, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'End Datestamp');

$table->addColumn('duration',Varien_Db_Ddl_Table::TYPE_INTEGER, 5, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => true,
        'primary'   => false,
        ), 'Message Duration in seconds');

$table->addColumn('message_status',Varien_Db_Ddl_Table::TYPE_INTEGER, 2, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Message Status');

$table->addColumn('status_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Status Code');

$table->addColumn('status_description',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Status Code');

$table->addColumn('xml_in',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Xml In');

$table->addColumn('xml_out',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        ), 'Xml Out');

$conn->createTable($table); 

/************************************************************************
Step : Create Erp Order Status Mapping Table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/erp_mapping_orderstatus'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_orderstatus'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
        'nullable'  => false,
        ), 'Erp Code');
$table->addColumn('status',Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'=>'processing'
        ), 'Order status');
$table->addColumn('state',Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'=>'processing'
        ), 'Order State');

$conn->createTable($table); 

/************************************************************************
Step : Create Erp Payment Mapping Table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/erp_mapping_payment'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_payment'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID');
$table->addColumn('erp_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 55, array(
        'nullable'  => false,
        ), 'Erp Code');
$table->addColumn('magento_code',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Magento Code');
$table->addColumn('payment_collected',Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
        ), 'PaymentCollected');

$conn->createTable($table); 

/************************************************************************
Step : Create Customer SKU table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/customer_sku'));
$table=$conn->newTable($this->getTable('epicor_comm/customer_sku'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');

$table->addColumn('product_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product ID');

$table->addColumn('customer_group_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Customer Group ID');

$table->addColumn('sku',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Sku');
$table->addColumn('description',Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Description');

$table->addForeignKey(
    $installer->getFkName(
            'catalog/product',
            'entity_id', 
            'epicor_comm/customer_sku', 
            'product_id'
    ),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$conn->createTable($table);

/************************************************************************
Step : Create Erp Country Mapping Table
*************************************************************************/

// drop tables
$conn->dropTable($this->getTable('epicor_comm/erp_mapping_country'));
$table=$conn->newTable($this->getTable('epicor_comm/erp_mapping_country'));
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
$table->addColumn('magento_id',Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Magento ID');
$conn->createTable($table); 


/************************************************************************
Step : Create Indexer Table
*************************************************************************/

$conn->dropTable($this->getTable('epicor_comm/indexer'));
$table=$conn->newTable($this->getTable('epicor_comm/indexer'));
$table->addColumn('id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');

$table->addColumn('index_data_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => false,
        ), 'Data Id');

$table->addColumn('created_at',Varien_Db_Ddl_Table::TYPE_DATETIME, 10, array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        ), 'Created Date');

$conn->createTable($table);

/************************************************************************
Step : Add Attributes to Products
*************************************************************************/

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->addAttribute('catalog_product', 'more_info_file', array(
    'group'                         => 'General',
    'label'                         => 'More Info',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'more_info_raw', array(
    'group'                         => 'General',
    'label'                         => 'More Info Raw Data',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

// add MPN attribute to products
$installer->addAttribute('catalog_product', 'mpn', array(
    'group'                         => 'General',
    'label'                         => 'MPN',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add UPC attribute to products
$installer->addAttribute('catalog_product', 'upc', array(
    'group'                         => 'General',
    'label'                         => 'UPC',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add EAN attribute to products
$installer->addAttribute('catalog_product', 'ean', array(
    'group'                         => 'General',
    'label'                         => 'EAN',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add ISBN attribute to products
$installer->addAttribute('catalog_product', 'isbn', array(
    'group'                         => 'General',
    'label'                         => 'ISBN',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add Brand attribute to products
$installer->addAttribute('catalog_product', 'brand', array(
    'group'                         => 'General',
    'label'                         => 'Brand',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => true,
    'comparable'                    => true,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => true,
));

// add PackSize attribute to products
$installer->addAttribute('catalog_product', 'pack_size', array(
    'group'                         => 'General',
    'label'                         => 'Pack Size',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => true,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add UOM attribute to products
$installer->addAttribute('catalog_product', 'uom', array(
    'group'                         => 'General',
    'label'                         => 'UOM',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => true,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

// add Lead Time attribute to products
$installer->addAttribute('catalog_product', 'lead_time', array(
    'group'                         => 'General',
    'label'                         => 'Lead time',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => true,
    'filterable'                    => false,
    'comparable'                    => true,
    'visible_on_front'              => true,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'google_feed', array(
    'group'                         => 'General',
    'label'                         => 'Show in Google Product Feed',
    'type'                          => 'int',
    'input'                         => 'boolean',
    'default'                       => '1',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'condition', array(
    'group'                         => 'General',
    'label'                         => 'Condition',
    'type'                          => 'varchar',
    'input'                         => 'select',
    'default'                       => 'new',
    'source'                        => 'productfeed/product_attribute_backend_condition',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'erp_image_main', array(
    'group'                         => 'Images',
    'label'                         => 'Main Image filename from Erp',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'default'                       => '',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'erp_image_thumb', array(
    'group'                         => 'Images',
    'label'                         => 'Thumb Image filename from Erp',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'default'                       => '',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'ftp_image_assigned', array(
    'group'                         => 'Images',
    'label'                         => 'Image assigned from FTP',
    'type'                          => 'int',
    'input'                         => 'boolean',
    'default'                       => '0',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'ftp_image_checked', array(
    'group'                         => 'Images',
    'label'                         => 'Last FTP check for this product',
    'type'                          => 'datetime',
    'input'                         => 'date',
    'default'                       => null,
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'ftp_image_id_main', array(
    'group'                         => 'Images',
    'label'                         => 'Main Image Id',
    'type'                          => 'int',
    'input'                         => 'text',
    'default'                       => '0',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'ftp_image_id_thumb', array(
    'group'                         => 'Images',
    'label'                         => 'Thumb Image Id',
    'type'                          => 'int',
    'input'                         => 'text',
    'default'                       => '0',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));

$installer->addAttribute('catalog_product', 'default_category_position', array(
    'group'                         => 'General',
    'label'                         => 'Default group Position',
    'type'                          => 'int',
    'input'                         => 'text',
    'default'                       => '',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));


$installer->endSetup();


/************************************************************************
Step : Add Attributes to Customers
*************************************************************************/

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 
$installer->addAttribute('customer', 'erpaccount_id', array(
    'group'                         => 'General',
    'label'                         => 'ERP Account',
    'type'                          => 'int',
    'input'                         => 'erpaccount',
    'default'                       => '',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,    
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
));


//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'erpaccount_id',
 '999'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'erpaccount_id');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->addAttribute('customer', 'previous_erpaccount', array(
    'label' => 'Previous ERP Account',
    'type' => 'varchar',
    'input' => 'text',
    'visible' => false,
    'required' => false
));


$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'previous_erpaccount',
 '1000'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'previous_erpaccount');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();

$helper = Mage::helper('epicor_comm/setup');
/* @var $helper Epicor_Comm_Helper_Setup */
/* previously in data-upgrade-1.0.0.0-1.0.0.0.php */  
try {
    $hadSolarsoft = count($conn->fetchAll("SELECT * from solarsoft_comm_erp_customer_group"));
} catch (Exception $exc) {
    $hadSolarsoft = false;
}

if ($hadSolarsoft) { 
    $helper->migrateData($this->getTable('epicor_comm/erp_catalog_category'), 'solarsoft_comm_erp_catalog_category_entity', $conn);	
    $helper->migrateData($this->getTable('epicor_comm/customer_erpaccount'), 'solarsoft_comm_erp_customer_group', $conn); 
    $helper->migrateData($this->getTable('epicor_comm/customer_erpaccount_address'), 'solarsoft_comm_erp_customer_group_address', $conn);	
		
    $helper->migrateData($this->getTable('epicor_comm/erp_mapping_country'), 'solarsoft_comm_erp_mapping_country', $conn);
    $helper->migrateData($this->getTable('epicor_comm/erp_mapping_orderstatus'), 'solarsoft_comm_erp_mapping_orderstatus', $conn);
    $helper->migrateData($this->getTable('epicor_comm/erp_mapping_payment'), 'solarsoft_comm_erp_mapping_payment', $conn);
    $helper->migrateData($this->getTable('epicor_comm/customer_sku'), 'solarsoft_comm_erp_customer_sku', $conn);
    $helper->migrateData($this->getTable('epicor_comm/message_log'), 'solarsoft_comm_message_log', $conn);
	
}

$installer->endSetup();

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
/* added to accommodate the solarsoft customer group table not having this column, so removed from initial definition to enable copying of data in migrate */  
$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 
        'account_type',
        array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 10,
        'default'   => 'Customer',
        'comment'   => 'Account Type'
        ));
$installer->endSetup(); 

if ($hadSolarsoft) { 
	// update the account_type attribute for the copied data	
    $erps = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();   
    foreach ($erps as $erp) {
        $erp->setAccountType('Customer');          
        $erp->save();
    }	
}
