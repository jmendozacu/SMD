<?php

/**
 * Upgrade - 1.0.0.62  to 1.0.0.63
 * 
 * Adding erp basket quote number to the object so its no longer in the session
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Quote / Order Goods totals

$conn->addColumn($installer->getTable('sales/quote'), 'basket_erp_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '20',
    'comment' => 'Basket Erp Quote Number'
));
$conn->addColumn($installer->getTable('sales/order'), 'basket_erp_quote_number', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '20',
    'comment' => 'Basket Erp Quote Number'
));

$installer->endSetup();
