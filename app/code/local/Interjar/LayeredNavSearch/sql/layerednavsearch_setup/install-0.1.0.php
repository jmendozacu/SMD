<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 15:27
 */
$installer = $this;
$installer->startSetup();
$attribute  = array(
    'group'             => "Display Settings",
    'type'              => 'text',
    'label'             => 'Add Search to Layered Navigation',
    'input'             => 'select',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'          => false,
    'visible'           => true,
    'visible_on_front'  => true,
    'user_defined'      => true
);
$installer->addAttribute('catalog_category', 'search_in_nav', $attribute);
$installer->endSetup();
