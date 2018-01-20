<?php

/**
 * Version 1.0.0.48 to 1.0.0.49 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$model = Mage::getResourceModel('epicor_common/access_element');
/* @var $model Epicor_Common_Model_Resource_Access_Element */
$model->regenerate('Mage_XmlConnect');

$installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET excluded = 0 WHERE module = 'Mage_XmlConnect'");

$element = $helper->addAccessElement('Mage_XmlConnect', '*', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());

$installer->endSetup();
