<?php

/**
 * Version 1.0.0.2 to 1.0.0.3 upgrade
 * 
 * Add branch pickup block to flexi theme
 */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */
/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */
if ($helper->isModuleEnabled('Epicor_Comm')) {
    if (!$helper->findLayoutBlock('epicor_comm/customer_branchpickup')) {
        $helper->createLayoutBlock('Branch Pickup', 'epicor_comm/customer_branchpickup', 'epicor_comm/customer/branchpickup.phtml', NULL, NULL, NULL, NULL, 'epicor_comm.branchpickup', NULL);
    }
}