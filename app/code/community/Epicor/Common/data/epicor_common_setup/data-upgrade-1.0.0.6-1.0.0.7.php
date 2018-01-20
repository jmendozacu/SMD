<?php

/**
 * Upgrade - 1.0.0.6 to 1.0.0.7
 * 
 * setting type on access groups
 */
$installer = $this;
$installer->startSetup();

$groups = Mage::getModel('epicor_common/access_group')->getCollection();
/* @var $model Epicor_Common_Model_Resource_Access_Group_Collection */

foreach ($groups->getItems() as $group) {
    /* @var $group Epicor_Common_Model_Access_Group */
    if (stripos($group->getEntityName(), 'Supplier') !== false) {
        $group->setType('supplier');
        $group->save();
    }
}

$rights = Mage::getModel('epicor_common/access_right')->getCollection();
/* @var $model Epicor_Common_Model_Resource_Access_Right_Collection */

foreach ($rights->getItems() as $right) {
    /* @var $group Epicor_Common_Model_Access_Right */
    if (stripos($right->getEntityName(), 'Supplier') !== false) {
        $right->setType('supplier');
        $right->save();
    }
}

$installer->endSetup();
