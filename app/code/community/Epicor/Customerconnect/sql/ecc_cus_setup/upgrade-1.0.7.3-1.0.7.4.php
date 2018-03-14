<?php

/**
 * Version 1.0.7.2 - 1.0.7.3 upgrade
 * AR Payments Installation Script
 */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/************************************************************************
Step 1: Add column to epicor_comm_erp_customer_group
*************************************************************************/
$table1 = $installer->getTable('epicor_comm/erp_customer_group');
if (!$conn->tableColumnExists($table1, 'is_arpayments_allowed')) {
    $conn->addColumn($table1, 'is_arpayments_allowed', array(
        'nullable' => true,
        'unsigned' => true,
        'primary' => false,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 1,
        'comment' => 'AR Payments Allowed, 2: Global Default, 0: No, 1: Yes, 3:Yes, no disputes',
        'default' => 2
    ));
}
/************************************************************************
Step 2: Add Entries to the Access Rights
*************************************************************************/
$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */
$writeGroup = $helper->loadAccessGroupByName('Customerconnect - Full Access');
$readGroup  = $helper->loadAccessGroupByName('Customerconnect - Read Only');
$module = 'Epicor_Customerconnect';
$section = 'Arpayments';
$helper->addAccessElement('Epicor_Customerconnect', 'Arpayments', '*', '', 'Access', 1);
$right = $helper->addAccessRight('Customerconnect - Arpayments - List Page - View');
$helper->addAccessRightElement($right->getId(), $module, $section, '*', '', 'Access','1');
$helper->addAccessGroupRight($writeGroup->getId(), $right->getId());
$helper->addAccessGroupRight($readGroup->getId(), $right->getId());
/************************************************************************
Step 3: Add column to quote/quote_item,order,order_item table
*************************************************************************/
$installer->addAttribute('order', 'ecc_arpayments_invoice', array('type'=>'boolean','default'=>0));
$installer->addAttribute('order', 'ecc_caap_sent', array('type'=>'boolean','default'=>0));
$installer->addAttribute('order', 'ecc_caap_message', array('type'=>'varchar','default'=>'Payment Not Sent','length'=>255));
$installer->addAttribute('order', 'ecc_arpayments_allocated_amount', array('type'=>'varchar','default'=>0));
$installer->addAttribute('order', 'ecc_arpayments_amountleft', array('type'=>'varchar','default'=>0));
$installer->addAttribute('order', 'ecc_arpayments_ispayment', array('type'=>'boolean','default'=>0));
$installer->addAttribute('quote', 'ecc_arpayments_allocated_amount', array('type'=>'varchar','default'=>0));
$installer->addAttribute('quote', 'ecc_arpayments_amountleft', array('type'=>'varchar','default'=>0));
$installer->addAttribute('quote', 'ecc_arpayments_ispayment', array('type'=>'boolean','default'=>0));
$installer->addAttribute('quote', 'ecc_arpayments_invoice', array('type'=>'boolean','default'=>0));
$installer->addAttribute('quote_item', 'ecc_arpayments_invoice_no', array('type'=>'varchar','default'=>null));
$installer->addAttribute('quote_item', 'ecc_arpayments_invoice', array('type'=>'boolean','default'=>0));
$installer->addAttribute('order_item', 'ecc_arpayments_invoice_no', array('type'=>'varchar','default'=>null));
$installer->addAttribute('order_item', 'ecc_arpayments_invoice', array('type'=>'boolean','default'=>0));
$installer->endSetup();