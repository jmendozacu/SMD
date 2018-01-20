<?php

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

/**
 * LISTS
 */

// ACCESS RIGHTS

// load existing access rights 
$listIndexRight = $installHelper->loadAccessRightByName('Customerconnect - Lists - Contracts - Index');    
$listDetailsRight = $installHelper->loadAccessRightByName('Customerconnect - Lists - Contracts - Details');  

// remove old access right
$oldIndex = $installHelper->loadAccessElement('Epicor_Lists', 'Contracts', 'index', '', 'Access');
$oldDetails = $installHelper->loadAccessElement('Customerconnect', 'Lists', 'details', '', 'Access');
$installHelper->removeElementFromRights($oldIndex->getId());
$installHelper->removeElementFromRights($oldDetails->getId());

// create new access right elements
$index = $installHelper->addAccessElement('Epicor_Customerconnect', 'Contracts', 'index', '', 'Access');
$details = $installHelper->addAccessElement('Epicor_Customerconnect', 'Contracts', 'details', '', 'Access');

//add elements to existing access rights
$installHelper->addAccessRightElementById($listIndexRight->getid(),$index->getId());
$installHelper->addAccessRightElementById($listDetailsRight->getid(),$details->getId());


$installer->endSetup();
