<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

// create default exclusions
$module = 'Epicor_Customerconnect';

$helper->addAccessElement($module,'Grid','clear','','Access',1);

$right = $helper->loadAccessRightByName('ECC Default - Sales Rep - CustomerConnect Access');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Account','index','account_information','view');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Account','index','period_balances','view');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Account','index','aged_balances','view');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Account','index','shipping_addresses','view');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Account','index','contacts','view');
$helper->addAccessRightElement($right->getId(),'Epicor_Customerconnect','Dashboard','index','customer_account_summary','view');

$installer->endSetup();
