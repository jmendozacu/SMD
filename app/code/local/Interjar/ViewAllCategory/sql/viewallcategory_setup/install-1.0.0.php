<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 19/01/2018
 * Time: 09:08
 */
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();
$viewAllAttributeCode = 'is_view_all_category';
$attribute  = array(
    'group'             => "General Information",
    'type'              => 'text',
    'label'             => 'View All Category',
    'input'             => 'select',
    'source'            => 'eav/entity_attribute_source_boolean',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'          => false,
    'visible'           => true,
    'visible_on_front'  => true,
    'user_defined'      => true,
    'default'           => Mage_Eav_Model_Entity_Attribute_Source_Boolean::VALUE_NO
);
$installer->addAttribute('catalog_category', $viewAllAttributeCode, $attribute);

// Run through all Categories and set the default value of No
/** @var Mage_Catalog_Model_Category $categories */
$categories = Mage::getModel('catalog/category');
/** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
$categoryCollection = $categories->getCollection();
$categoryCollection->addAttributeToSelect($viewAllAttributeCode);
foreach ($categoryCollection as $category) {
    $category->setIsViewAllCategory(Mage_Eav_Model_Entity_Attribute_Source_Boolean::VALUE_NO);
    $category->getResource()->saveAttribute($category, $viewAllAttributeCode);
}

$installer->endSetup();
