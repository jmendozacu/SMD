<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Create Sales Rep Account Table
$tableName = $this->getTable('epicor_salesrep/account');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('sales_rep_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Sale Rep Id');
$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Sales Rep Name');

$table->addIndex(
        $installer->getIdxName(
                $tableName, array('sales_rep_id')
        ), 'sales_rep_id');

$conn->createTable($table);


// Create Sales Rep Hierarchy Table

$tableName = $this->getTable('epicor_salesrep/hierarchy');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('child_sales_rep_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Child Id');
$table->addColumn('parent_sales_rep_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Parent Id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('child_sales_rep_account_id')
        ), 'child_sales_rep_account_id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('parent_sales_rep_account_id')
        ), 'parent_sales_rep_account_id');


$foreignKeyTableName = $this->getTable('epicor_salesrep/account');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 'id', $tableName, 'child_sales_rep_account_id'), 'child_sales_rep_account_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 'id', $tableName, 'parent_sales_rep_account_id'), 'parent_sales_rep_account_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);





// Create Sales Rep Erp Account Linkage Table

$tableName = $this->getTable('epicor_salesrep/erp_account');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('sales_rep_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Sales Rep Id');
$table->addColumn('erp_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Erp Account Id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('sales_rep_account_id')
        ), 'sales_rep_account_id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('erp_account_id')
        ), 'erp_account_id');


$foreignKeyTableName = $this->getTable('epicor_salesrep/account');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 'id', $tableName, 'sales_rep_account_id'), 'sales_rep_account_id', $foreignKeyTableName, 'id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$foreignKeyTableName = $this->getTable('epicor_comm/customer_erpaccount');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 'entity_id', $tableName, 'erp_account_id'), 'erp_account_id', $foreignKeyTableName, 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);



// Create Sales Rep Pricing Rules Table

$tableName = $this->getTable('epicor_salesrep/pricing_rule');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'ID');
$table->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Pricing Rule Name');
$table->addColumn('sales_rep_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Sale Rep Account Id');
$table->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
        ), 'From Date');
$table->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
        ), 'To Date');
$table->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => FALSE
        ), 'Is Active');
$table->addColumn('priority', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    'default' => 0,
        ), 'Sort Order of Rules');
$table->addColumn('action_operator', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Pricing Rule Base On');
$table->addColumn('action_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    'default' => 0,
        ), 'Sale Rep Margin');
$table->addColumn(
        'conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '4G', array(
    'nullable' => false,
    'default' => ''
        ), 'Conditions'
);


$table->addIndex(
        $installer->getIdxName(
                $tableName, array('sales_rep_account_id')
        ), 'sales_rep_account_id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('is_active')
        ), 'is_active');

$conn->createTable($table);





// Create Sales Rep Pricing Rule Products Table

$tableName = $this->getTable('epicor_salesrep/pricing_rule_product');
$conn->dropTable($tableName);
$table = $conn->newTable($tableName);
$table->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
        ), 'Entity ID');
$table->addColumn('pricing_rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Pricing Rule Id');
$table->addColumn('sales_rep_account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Sales Rep Id');
$table->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
        ), 'Pricing Id');
;
$table->addColumn('action_operator', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Pricing Rule Base On');
$table->addColumn('action_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    'default' => 0,
        ), 'Sale Rep Margin');
$table->addColumn('lowest_price_allowed', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Lowest Price');
$table->addColumn('is_valid', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    'nullable' => false,
    'default' => FALSE
        ), 'Is Valid');

$table->addIndex(
        $installer->getIdxName(
                $tableName, array('pricing_rule_id')
        ), 'pricing_rule_id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('sales_rep_account_id')
        ), 'sales_rep_account_id');
$table->addIndex(
        $installer->getIdxName(
                $tableName, array('product_id')
        ), 'product_id');


$foreignKeyTableName = $this->getTable('epicor_salesrep/account');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 
                'id', 
                $tableName, 
                'sales_rep_account_id'
                ), 
        'sales_rep_account_id', 
        $foreignKeyTableName, 
        'id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$foreignKeyTableName = $this->getTable('epicor_salesrep/pricing_rule');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 
                'id', 
                $tableName, 
                'pricing_rule_id'
                ), 
        'pricing_rule_id', 
        $foreignKeyTableName, 
        'id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$foreignKeyTableName = $this->getTable('catalog/product');
$table->addForeignKey(
        $installer->getFkName(
                $foreignKeyTableName, 
                'entity_id', 
                $tableName, 
                'product_id'
                ), 
        'product_id', 
        $foreignKeyTableName, 
        'entity_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, 
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );


$conn->createTable($table);

$conn->addColumn($installer->getTable('sales/quote'), 'epicor_salesrep_lowest_price', array(
    'unsigned' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'length' => '16,4',
    'nullable' => true,
    'comment' => 'Lowest Price',
    'default' => '0.0000'
));


$installer->endSetup();


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 
$installer->addAttribute('customer', 'sales_rep_id', array(
    'group'                         => 'General',
    'label'                         => 'Sales Rep Id',
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


//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'sales_rep_id',
 '999'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'sales_rep_id');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->addAttribute('customer', 'sales_rep_account_id', array(
    'group'                         => 'General',
    'label'                         => 'Sales Rep Account Id',
    'type'                          => 'int',
    'input'                         => 'salesrepaccount',
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
 'sales_rep_account_id',
 '999'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'sales_rep_account_id');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$installer->endSetup();
