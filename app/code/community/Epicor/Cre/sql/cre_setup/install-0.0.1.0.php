<?php

/**
 * Installs CRE tables && Add CRE into access rights exclusions
 *
 * @category    Epicor
 * @package     Epicor_Cre
 * @author      Epicor Web Sales Team
 */

$installer = $this;
$installer->startSetup();

$conn  = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$table = $conn->newTable($this->getTable('cre/token'));
/* @var $table Varien_Db_Ddl_Table */
if(!$conn->isTableExists($this->getTable('cre/token'))){

    $table->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true
    ), 'Entity ID');

    $table->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true
    ), 'Customer Id');

    $table->addColumn('ccv_token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'CCV Token');
    $table->addColumn('cvv_token', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'CVV Token');

    $table->addColumn('card_type', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
        'nullable' => true
    ), 'Card Type');

    $table->addColumn('last4', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(), 'Last 4 Digits');

    $table->addColumn('expiry_month', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(), 'Expiry Month');

    $table->addColumn('expiry_year', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(), 'Expiry Year');

    $table->addColumn('cre_transaction_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'CRE transaction Id');

    $table->addIndex($installer->getIdxName($this->getTable('cre/token'), array(
        'customer_id'
    )), 'customer_id');
    $table->addForeignKey($installer->getFkName($this->getTable('customer/entity'), 'entity_id', $this->getTable('cre/token'), 'customer_id'), 'customer_id', $this->getTable('customer/entity'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
    $conn->createTable($table);

    //Add the transaction id into the order table
    $conn->addColumn($installer->getTable('sales/order_payment'), 'cre_transaction_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'CRE transaction Id'
    ));

    //Add the transaction id into the quote
    $conn->addColumn($installer->getTable('sales/quote_payment'), 'cre_transaction_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'comment' => 'CRE transaction Id'
    ));

    //Access Control DB Entry
    $helper  = Mage::helper('epicor_common/setup');
    /* @var $helper Epicor_Common_Helper_Setup */
    $element = $helper->addAccessElement('Epicor_Cre', '*', '*', '', 'Access', 1);
    $helper->removeElementFromRights($element->getId());
    $installer->endSetup();
}