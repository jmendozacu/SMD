<?php

$installer = $this;
$installer->startSetup();

$ignoreModules = array(
    'Mage_Cms',
    'Mage_CatalogSearch',
    'Epicor_Customerconnect',
    'Epicor_Supplierconnect'
);

$ignoreActions = array(
    'Mage_Customer' => array(
        'Account' => array(
            'login',
            'loginPost'
        )
    ),
    'Epicor_Common ' => array(
        'Account' => array(
            'edit',
            'editPost'
        )
    )
);

$collection = Mage::getModel('epicor_common/access_element')->getCollection();
/* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
$collection->addFieldToFilter('excluded', 1);

foreach ($collection->getItems() as $element) {
    
    /* @var $collection Epicor_Common_Model_Access_Element */
    if (!in_array($element->getModule(), $ignoreModules) && (
            !isset($ignoreActions[$element->getModule()]) ||
            !isset($ignoreActions[$element->getModule()][$element->getController()]) ||
            !in_array($element->getAction(), $ignoreActions[$element->getModule()][$element->getController()]))
    ) {
        $element->setPortalExcluded(1);
    } else {
        $element->setPortalExcluded(0);
    }
    
    $element->save();
}

$installer->endSetup();
