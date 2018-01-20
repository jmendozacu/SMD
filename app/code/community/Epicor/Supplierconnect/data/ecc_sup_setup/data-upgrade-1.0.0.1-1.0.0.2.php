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
$module = 'Epicor_Supplierconnect';

$helper->addAccessElement($module,'Grid','clear','','Access',1);

// add all Elements for this module
$helper->regenerateModuleElements($module);

// set up access right

$readWriteGroup = $helper->addAccessGroup('Supplierconnect - Full Access');
$readOnlyGroup = $helper->addAccessGroup('Supplierconnect - Read Only');

// set up rights in access right
$right = $helper->addAccessRight('Supplierconnect - Dashboard - Access Page');
$helper->addAccessRightElement($right->getId(),$module,'Account','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Invoices - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Invoices','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Invoices - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Invoices','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Details Page - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Orders','update','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Confirm New List - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','new','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Confirm New List - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Orders','confirmnew','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Confirm Changes List - View');
$helper->addAccessRightElement($right->getId(),$module,'Orders','changes','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Orders - Confirm Changes List - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Orders','confirmchanges','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Parts - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Parts','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Parts - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Parts','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - Payments - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Payments','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - RFQ - List Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Rfq','index','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - RFQ - Details Page - View');
$helper->addAccessRightElement($right->getId(),$module,'Rfq','details','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());
$helper->addAccessGroupRight($readOnlyGroup->getId(),$right->getId());

$right = $helper->addAccessRight('Supplierconnect - RFQ - Details Page - Edit');
$helper->addAccessRightElement($right->getId(),$module,'Rfq','update','','Access');
$helper->addAccessGroupRight($readWriteGroup->getId(),$right->getId());

// set up default group for access right

$installer->endSetup();