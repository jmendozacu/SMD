<?php

/**
 * Sort out default group permissions fue to error in element generation process that was naming controllers in folders wrong
 */

set_time_limit(0);

$installer = $this;
$installer->startSetup();

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$table = $installer->getTable('epicor_common/access_element');

$installer->run("
    INSERT INTO `" . $table . "` 
    (`module`,`controller`,`action`,`block`,`action_type`,`excluded`) 
        VALUES
    ('Mage_XmlConnect','Paypal_mecl','start','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','return','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','review','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','orderReview','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','shippingMethods','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','saveShippingMethod','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','placeOrder','','Access',1),
    ('Mage_XmlConnect','Paypal_mecl','cancel','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','index','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','saveShippingAddress','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','shippingMethods','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','saveShippingMethod','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','cartTotals','','Access',1),
    ('Mage_XmlConnect','Paypal_mep','saveOrder','','Access',1)
");

$helper->regenerateModuleElements('Mage_Cms');
$helper->regenerateModuleElements('Mage_Customer');
$helper->regenerateModuleElements('Mage_CatalogSearch');
$helper->regenerateModuleElements('Mage_Rss');
$helper->regenerateModuleElements('Mage_ProductAlert');
$helper->regenerateModuleElements('Mage_Api');
$helper->regenerateModuleElements('Mage_Oauth');
$helper->regenerateModuleElements('Mage_Persistent');
$helper->regenerateModuleElements('Mage_XmlConnect');
$helper->regenerateModuleElements('Mage_Core');
$helper->regenerateModuleElements('Mage_Directory');
$helper->regenerateModuleElements('Mage_Catalog');
$helper->regenerateModuleElements('Mage_Sales');
$helper->regenerateModuleElements('Mage_Shipping');
$helper->regenerateModuleElements('Mage_Paygate');
$helper->regenerateModuleElements('Mage_Checkout');
$helper->regenerateModuleElements('Mage_Paypal');
$helper->regenerateModuleElements('Mage_Poll');
$helper->regenerateModuleElements('Mage_GoogleCheckout');
$helper->regenerateModuleElements('Mage_Review');
$helper->regenerateModuleElements('Mage_Tag');
$helper->regenerateModuleElements('Mage_Wishlist');
$helper->regenerateModuleElements('Mage_GiftMessage');
$helper->regenerateModuleElements('Mage_Contacts');
$helper->regenerateModuleElements('Mage_Sendfriend');
$helper->regenerateModuleElements('Mage_Authorizenet');
$helper->regenerateModuleElements('Mage_Bundle');
$helper->regenerateModuleElements('Mage_Captcha');
$helper->regenerateModuleElements('Mage_Centinel');
$helper->regenerateModuleElements('Mage_Compiler');
$helper->regenerateModuleElements('Mage_Newsletter');
$helper->regenerateModuleElements('Mage_Downloadable');
$helper->regenerateModuleElements('Epicor_B2b');
$helper->regenerateModuleElements('Epicor_Common');
$helper->regenerateModuleElements('Epicor_Comm');
$helper->regenerateModuleElements('Epicor_ErpSimulator');
$helper->regenerateModuleElements('Epicor_Esdm');
$helper->regenerateModuleElements('Epicor_FlexiTheme');
$helper->regenerateModuleElements('Epicor_ProductFeed');
$helper->regenerateModuleElements('Epicor_QuickOrderPad');
$helper->regenerateModuleElements('Epicor_Quotes');
$helper->regenerateModuleElements('Epicor_Verifone');
$helper->regenerateModuleElements('Phoenix_Moneybookers');

$right = $helper->loadAccessRightByName('Customer - Full Access');
/* @var $model Epicor_Common_Model_Access_Right */

$collection = Mage::getModel('epicor_common/access_right_element')->getCollection();
$assignedIds = array();
foreach ($collection->getItems() as $element) {
    $assignedIds[] = $element->getElementId();
}

$collection = Mage::getModel('epicor_common/access_element')->getCollection();
/* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
$collection->addFieldToFilter('excluded', 0);
$collection->addFieldToFilter('id', array('nin' => $assignedIds));

foreach ($collection->getItems() as $element) {
    /* @var $collection Epicor_Common_Model_Access_Element */
    $helper->addAccessRightElementById($right->getId(), $element->getId());
}

$installer->endSetup();
