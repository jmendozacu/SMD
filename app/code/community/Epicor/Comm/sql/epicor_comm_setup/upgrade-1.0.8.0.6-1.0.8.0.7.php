<?php
/*
 *  upgrade script to create ERP account tax_exemp attribute and add column in quote table
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$this->startSetup();

/*Create an ERP customer account attribute tax_exempt_reference*/
$table = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($table, 'is_tax_exempt')) {
    $conn->addColumn($table, 'is_tax_exempt', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'Allow Tax Exempt, 2: Default, 0: Disabled, 1: Enabled',
        'default' => 2
    ));
}
/*add column(tax_exempt_reference) to sales_flat_quote table*/
$this->getConnection()->addColumn($this->getTable('sales/quote'), 'tax_exempt_reference', "varchar(150) default null");
$this->getConnection()->addColumn($this->getTable('sales/order'), 'tax_exempt_reference', "varchar(150) default null");

$this->endSetup();
