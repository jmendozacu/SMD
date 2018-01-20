<?php

/**
 * Version 1.0.0.4 to 1.0.0.5 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$model = Mage::getResourceModel('epicor_common/access_element');
/* @var $model Epicor_Common_Model_Resource_Access_Element */
$model->regenerate('Epicor_Comm');
$model->regenerate('Mage_XmlConnect');

$installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET portal_excluded = 0 WHERE module = 'Epicor_Comm' AND controller = 'Data'");
$installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET portal_excluded = 0 WHERE module = 'Epicor_Comm' AND controller = 'Configurator'");
$installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET portal_excluded = 0 WHERE module = 'Mage_XmlConnect'");
$installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET portal_excluded = 0 WHERE module = 'Mage_Cms' AND controller = 'Index' AND action = 'index'");

$element = $helper->addAccessElement('Mage_XmlConnect', '*', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());
$element->setPortalExcluded(1);
$element->save();

$element = $helper->addAccessElement('Epicor_Comm', 'Data', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());
$element->setPortalExcluded(1);
$element->save();

$element = $helper->addAccessElement('Epicor_Comm', 'Configurator', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());
$element->setPortalExcluded(1);
$element->save();

$installer->endSetup();
