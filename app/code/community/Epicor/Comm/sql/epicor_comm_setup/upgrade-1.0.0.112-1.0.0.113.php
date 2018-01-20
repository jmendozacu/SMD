<?php

/**
 * Upgrade - 1.0.0.112 to 1.0.0.113
 * 
 * Add mobile number to customer
 */

/************************************************************************
Step : Add Attributes to Customers
*************************************************************************/

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 
$installer->addAttribute('customer', 'mobile_number', array(
    'group'                         => 'General',
    'label'                         => 'Mobile Number',
    'type'                          => 'varchar',
    'input'                         => 'text',
    'default'                       => '',
    'required'                      => false,
    'user_defined'                  => true,
    'searchable'                    => false,    
    'filterable'                    => false,
    'comparable'                    => false,
    'visible_on_front'              => false,
    'visible_in_advanced_search'    => false,
    'is_user_defined'   => 0,
    'is_system'         => 1,
    'is_visible'        => 1,
    'sort_order'        => 125,
));


//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');


$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'mobile_number',
 '125'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mobile_number');
$oAttribute->setData('used_in_forms', array(
            'customer_account_create',
            'customer_account_edit',
            'checkout_register',
            'adminhtml_customer',
            'adminhtml_checkout')
        );
$oAttribute->save();
$installer->endSetup();
