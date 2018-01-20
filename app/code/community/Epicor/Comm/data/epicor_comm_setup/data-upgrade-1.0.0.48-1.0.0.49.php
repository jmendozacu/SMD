<?php

/**
 * Version 1.0.0.48 to 1.0.0.49 upgrade
 * 
 */
$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

try{    
    $model = Mage::getResourceModel('epicor_common/access_element');
    /* @var $model Epicor_Common_Model_Resource_Access_Element */
    $model->regenerate('Epicor_Comm');

    $installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET excluded = 0 WHERE module = 'Epicor_Comm' AND controller = 'Data'");
    $installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET excluded = 0 WHERE module = 'Epicor_Comm' AND controller = 'Configurator'");
    $installer->run("UPDATE " . $this->getTable('epicor_common/access_element') . " SET excluded = 0 WHERE module = 'Mage_Cms' AND controller = 'Index' AND action = 'index'");

    $element = $helper->addAccessElement('Epicor_Comm', 'Data', '*', '', 'Access', 1);
    $helper->removeElementFromRights($element->getId());

    $element = $helper->addAccessElement('Epicor_Comm', 'Configurator', '*', '', 'Access', 1);
    $helper->removeElementFromRights($element->getId());

    $right = $helper->loadAccessRightByName('Customer - Full Access ');
    $element = $helper->loadAccessElement('Mage_Cms', 'Index', 'index', '', 'Access');
    $helper->addAccessRightElementById($right->getid(),$element->getId());

    
} catch (Exception $ex) {
    Mage::log('epicor comm data upgrade script 1.0.0.48-1.0.0.49 failed: '); 
    Mage::log($ex);
}
$installer->endSetup();