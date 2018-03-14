<?php

/**
 * Upgrade - 1.0.9.0.7-1.0.9.0.8
 * 
 */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$attributeExists = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product','pricing_sku')->getId();

if ($attributeExists === null) {
    $installer->addAttribute('catalog_product', 'pricing_sku', array(
        'group'                         => 'General',
        'label'                         => 'Pricing SKU',
        'type'                          => 'varchar',
        'input'                         => 'text',
        'required'                      => false,
        'user_defined'                  => true,
        'searchable'                    => false,
        'filterable'                    => false,
        'comparable'                    => false,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => false,
        'used_in_product_listing'       => true
    ));

    $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
    $attributeSets = Mage::getModel('eav/entity_attribute_set')
                    ->getCollection()
                    ->setEntityTypeFilter($entityTypeId)
                    ->addFieldToSelect('attribute_set_id');
    foreach ($attributeSets as $attributeSet) {
        $setId = $attributeSet->getAttributeSetId();
        $installer->addAttributeToGroup($entityTypeId, $setId, 'General', 'pricing_sku', 5);
    }
}

$installer->endSetup();