<?php

/**
 * Upgrade - 1.0.0.87 to 1.0.0.88
 * 
 * Remove the default definition of 1 from stockleveldisplay 
 */

/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'stockleveldisplay', 'default_value','');
$installer->updateAttribute('catalog_product', 'stocklimitlow', 'default_value','');
$installer->updateAttribute('catalog_product', 'stocklimitlow', 'used_in_product_listing', '1');
$installer->updateAttribute('catalog_product', 'stocklimitnone', 'default_value','');
$installer->updateAttribute('catalog_product', 'stocklimitnone', 'used_in_product_listing', '1');

$installer->endSetup();


/***********************************************************************/
// Get the product Ids you want to affect. ie all
/***********************************************************************/

$productIds = Mage::getResourceModel('catalog/product_collection')
        ->getAllIds();

// Now create an array of attribute_code => values
$attributeData = array( 'stockleveldisplay' => ''
                       ,'stocklimitlow' => ''                                               
                       ,'stocklimitnone' => ''
                );

//Update store 0 values. These will be the default. Actual store values might have been changed
Mage::getSingleton('catalog/product_action')
                                    ->updateAttributes($productIds, $attributeData, 0);

