<?php

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * Step 1: Add created by to quote table
 */
$table = $installer->getTable('quotes/quote');

$conn->addColumn(
    $table, 'created_by',
    array(
    'nullable' => true,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'comment' => 'Created By',
    )
);

$quotes = Mage::getModel('quotes/quote')->getCollection();

foreach ($quotes as $quote) {
    /* @var $quote Epicor_Quotes_Model_quote */
    $customer = $quote->getCustomer(true);
    /* @var $customer Epicor_Comm_Model_Customer */
    $name = $customer->getName();
    $email = $customer->getEmail();

    $quote->setCreatedBy($name . ' (' . $email . ')');
    $quote->save();
}

$installer->endSetup();
