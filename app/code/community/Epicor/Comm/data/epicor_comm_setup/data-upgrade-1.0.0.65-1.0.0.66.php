<?php

/**
 * Upgrade - 1.0.0.65 to 1.0.0.66
 * 
 * Updating reorderable flag on all products
 */

// Get the product Ids you want to affect. For me it was all downloadable products
$productIds = Mage::getResourceModel('catalog/product_collection')
        ->getAllIds();

// Now create an array of attribute_code => values
$attributeData = array('reorderable' => true);

// Set the store to affect. I used admin to change all default values
// Now Update the attribute(s) for the given products.
Mage::getSingleton('catalog/product_action')
        ->updateAttributes($productIds, $attributeData, 0);
