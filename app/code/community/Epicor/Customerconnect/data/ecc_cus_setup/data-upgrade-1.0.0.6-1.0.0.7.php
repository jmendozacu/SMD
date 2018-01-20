<?php

/**
 * Version 1.0.0.6 to 1.0.0.7 upgrade
 * 
 * Adds excluded elements for access rights 
 */

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

// create default exclusions
$module = 'Epicor_Customerconnect';

$helper->addAccessElement($module,'Grid','clear','','Access',1);

// add all Elements for this module
$helper->regenerateModuleElements($module);

// set up access right

$readWriteGroup = $helper->addAccessGroup('Customerconnect - Full Access');
$readOnlyGroup = $helper->addAccessGroup('Customerconnect - Read Only');

// set up rights in access right
$right = $helper->addAccessRight('Customerconnect - Account Information - Access Page');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Info Pane - View');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','account_information','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Period Balances - View');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','period_balances','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Aged Balances - View');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','aged_balances','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Shipping Addresses - View');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','shipping_addresses','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Shipping Addresses - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Account','saveShippingAddress','','Access');
$helper->addAccessRightElement($right->getId(),$module,'Account','deleteShippingAddress','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Contacts - View');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','contacts','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Contacts - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Account','saveContact','','Access');
$helper->addAccessRightElement($right->getId(),$module,'Account','deleteContact','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Contacts - Manage Permissions');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','manage_permissions','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Account Information - Billing Addresses - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Account','saveBillingAddress','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Dashboard - Access Page');
$helper->addAccessRightElement($right->getId(),$module,'Dashboard','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Dashboard - Account Summary - View');
$helper->addAccessRightElement($right->getId(),$module,'Dashboard','index','customer_account_summary','view');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Invoices - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Invoices','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Invoices - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Invoices','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Orders - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Orders - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Orders - Re-order');
$helper->addAccessRightElement($right->getId(),$module,'Orders','reorder','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Payments - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Payments','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - RMAs - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Rmas','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Service Calls - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Servicecalls','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Shipments - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Shipments','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Customerconnect - Shipments - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Shipments','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

// set up default group for access right

$installer->endSetup();