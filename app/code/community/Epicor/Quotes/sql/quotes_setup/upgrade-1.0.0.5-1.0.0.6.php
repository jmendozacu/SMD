<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * Step  1: create new Quote Customer table
 */
$conn->dropTable($this->getTable('quotes/quote_customer'));
$table = $conn->newTable($installer->getTable('quotes/quote_customer'));

$table->addColumn(
    'entity_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER, 
    null, 
    array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 
    'Entity ID'
);

$table->addColumn(
    'quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    ), 'Customer Id'
);

$table->addColumn(
    'customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
    array(
    'unsigned' => true,
    'nullable' => false,
    ), 'Customer Id'
);

$table->addForeignKey(
    $installer->getFkName(
        $this->getTable('quotes/quote'), 
        'entity_id', 
        $this->getTable('quotes/quote_customer'), 
        'quote_id'
    ), 
    'quote_id',
    $this->getTable('quotes/quote'), 
    'entity_id', 
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addForeignKey(
    $installer->getFkName(
        $this->getTable('customer/entity'), 
        'entity_id', 
        $this->getTable('quotes/quote_customer'), 
        'customer_id'
    ), 
    'customer_id',
    $this->getTable('customer/entity'), 
    'entity_id', 
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->createTable($table);

/**
 * Step 2: Add ERP account ID column to quote table
 */

$table = $installer->getTable('quotes/quote');

$conn->addColumn(
    $table, 
    'erp_account_id',
    array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'ERP Account ID',
    )
);

$conn->addColumn(
    $table, 
    'is_global',
    array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment' => 'Is Quote global to ERP Account',
        'default' => 0,
    )
);

$conn->addColumn(
    $table, 
    'currency_code',
    array(
        'nullable' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 20,
        'comment' => 'Quote Currency Code',
    )
);

$conn->addForeignKey(
    $installer->getFkName(
        $this->getTable('epicor_comm/erp_customer_group'), 
        'entity_id', 
        $this->getTable('quotes/quote'),
        'erp_account_id'
    ), 
    $this->getTable('quotes/quote'), 
    'erp_account_id', 
    $this->getTable('epicor_comm/erp_customer_group'),
    'entity_id', 
    Varien_Db_Ddl_Table::ACTION_CASCADE, 
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->addIndex(
        $this->getTable('quotes/quote'),
        $installer->getIdxName(
            $this->getTable('quotes/quote'), 
            array('erp_account_id')
        ),
        'erp_account_id'
);

/**
 * Step 3: Migrate Data from old quote customer ID column to new Table
 */

$quotes = Mage::getModel('quotes/quote')->getCollection();
/* @var $quotes Epicor_Quotes_Model_Mysql4_Quote_Collection */

$currencyCodes = array();

foreach ($quotes as $quote) {
    /* @var $quote Epicor_Quotes_Model_Quote */
    
    $quoteCustomer = Mage::getModel('quotes/quote_customer');
    /* @var $quoteCustomer Epicor_Quotes_Model_Quote_Customer */
    
    $quoteCustomer->setCustomerId($quote->getCustomerId());
    $quoteCustomer->setQuoteId($quote->getId());
    $quoteCustomer->save();
    
    $customer = Mage::getModel('customer/customer')->load($quote->getCustomerId());
    /* @var $customer Epicor_Comm_Model_Customer */
    
    $quote->setErpAccountId($customer->getErpaccountId());
    $quote->setCurrencyCode($customer->getStore()->getBaseCurrencyCode());
    $quote->save();
}

/**
 * Step 4: Drop the old customer ID column
 */

$conn->dropColumn($table, 'customer_id');

$installer->endSetup();
