<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getTable('epicor_common/access_element');

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$element = $helper->addAccessElement('Epicor_Comm', 'Configurableproducts', '*', '', 'Access', 1);
$helper->removeElementFromRights($element->getId());

$installer->endSetup();

?>