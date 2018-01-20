<?php

/**
 * Add duplicate RFQ to access groups
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

// create default exclusions
$module = 'Epicor_Customerconnect';
$section = 'Rfqs';
// set up access right

$helper->addAccessElement($module, $section, 'duplicate', '', 'Access');

// set up rights in access right
$right = $helper->loadAccessRightByName('Customerconnect - Rfqs - Add/Edit');
$helper->addAccessRightElement($right->getId(), $module, $section, 'duplicate', '', 'Access');

$installer->endSetup();
