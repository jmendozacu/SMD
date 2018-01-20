<?php

/**
 * Version 1.0.0.5 to 1.0.0.6 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$element = $helper->addAccessElement('Epicor_Common', 'File', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());
$element->setPortalExcluded(1);
$element->save();

$element = $helper->addAccessElement('Epicor_Comm', 'Message', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());
$element->setPortalExcluded(1);
$element->save();

$installer->endSetup();
