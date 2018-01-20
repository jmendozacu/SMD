<?php

/**
 * Version 1.0.0.27 to 1.0.0.28 upgrade
 * 
 * Adds excluded elements for access rights 
 */

$installer = $this;
$installer->startSetup();
$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */
try{
    
    $element = $helper->addAccessElement('Epicor_Comm','Data','offlineorders','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','schedulemsq','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','schedulesod','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','scheduleimage','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','responder','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','logclean','','Access',1);
    $helper->removeElementFromRights($element->getId());
    $element = $helper->addAccessElement('Epicor_Comm','Data','indexproduct','','Access',1);
    $helper->removeElementFromRights($element->getId());

} catch (Exception $ex) {   
    Mage::log('Epicor_comm data upgrade script 1.0.0.27-1.0.0.28.php failed');
    Mage::log($ex);
}

$installer->endSetup();