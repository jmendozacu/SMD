<?php

$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->dropTable($this->getTable('quotes/quote'));
$table=$conn->newTable($this->getTable('quotes/quote'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('customer_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Customer Id');
$table->addColumn('status_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Status Id');
$table->addColumn('expires', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Expiry Date');
$table->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Created At');
$table->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated At');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('quotes/quote'),
            array('customer_id')
        ),
        'customer_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('customer/entity'),
                'entity_id',
                $this->getTable('quotes/quote'),
                'customer_id'), 
        'customer_id',
        $this->getTable('customer/entity'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);

$conn->dropTable($this->getTable('quotes/quote_product'));
$table=$conn->newTable($this->getTable('quotes/quote_product'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('quote_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Quote Id');
$table->addColumn('product_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Product Id');
$table->addColumn('orig_qty',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Original Quantity');
$table->addColumn('orig_price',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Original Price');
$table->addColumn('new_qty',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'New Quantity');
$table->addColumn('new_price',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'New Price');
$table->addColumn('note',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Note');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('quotes/quote_product'),
            array('quote_id')
        ),
        'quote_id');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('quotes/quote_product'),
            array('product_id')
        ),
        'product_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('quotes/quote'),
                'entity_id',
                $this->getTable('quotes/quote_product'),
                'quote_id'), 
        'quote_id',
        $this->getTable('quotes/quote'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);



$conn->dropTable($this->getTable('quotes/quote_note'));
$table=$conn->newTable($this->getTable('quotes/quote_note'));
$table->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID');
$table->addColumn('quote_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Quote Id');
$table->addColumn('admin_id',Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Admin User Id');
$table->addColumn('note',Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Note');
$table->addIndex(
        $installer->getIdxName(
            $this->getTable('quotes/quote_product'),
            array('quote_id')
        ),
        'quote_id');
$table->addForeignKey(
        $installer->getFkName(
                $this->getTable('quotes/quote'),
                'entity_id',
                $this->getTable('quotes/quote_note'),
                'quote_id'), 
        'quote_id',
        $this->getTable('quotes/quote'), 
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);

$conn->createTable($table);


$installer->endSetup();
