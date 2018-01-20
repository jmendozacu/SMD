<?php

/**
 * Upgrade - 1.0.6.0.9-1.0.6.0.10
 * 
 * Adding location name and gqr line number to quote lines and order lines
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$helper = Mage::helper('epicor_comm/setup');
/* @var $helper Epicor_Comm_Helper_Setup */
$helper->addAccessElement('Epicor_Common', 'Account', 'changeForgotten', null, 'Access', 1, 1);