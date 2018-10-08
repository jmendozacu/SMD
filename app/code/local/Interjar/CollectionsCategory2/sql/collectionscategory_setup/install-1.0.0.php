<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 24/08/2018
 * Time: 09:49
 */
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();
$viewAllAttributeCode = 'collection_display';
$attribute  = array(
    'group'             => "General Information",
    'type'              => 'text',
    'label'             => 'Collection Display',
    'input'             => 'select',
    'source'            => 'collectionscategory/entity_attribute_source_collection_display',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'required'          => false,
    'visible'           => true,
    'visible_on_front'  => true,
    'user_defined'      => true,
    'default'           => Interjar_CollectionsCategory_Model_Entity_Attribute_Source_Collection_Display::VALUE_LIST
);
$installer->addAttribute('catalog_category', $viewAllAttributeCode, $attribute);

// Run through all Categories and set the default value of No
/** @var Mage_Catalog_Model_Category $categories */
$categories = Mage::getModel('catalog/category');
/** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
$categoryCollection = $categories->getCollection();
$categoryCollection->addAttributeToSelect($viewAllAttributeCode);
foreach ($categoryCollection as $category) {
    $category->setCollectionDisplay(
        Interjar_CollectionsCategory_Model_Entity_Attribute_Source_Collection_Display::VALUE_LIST
    );
    $category->getResource()->saveAttribute(
        $category, $viewAllAttributeCode
    );
}

$installer->endSetup();
