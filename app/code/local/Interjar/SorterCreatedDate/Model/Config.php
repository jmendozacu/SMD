<?php
/**
 * Created by PhpStorm.
 * User: andreispanu
 * Date: 17/09/2018
 * Time: 15:36
 */

class Interjar_SorterCreatedDate_Model_Config extends Mage_Catalog_Model_Config
{
    /**
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = parent::getAttributeUsedForSortByArray();
        if (!isset($options['created_at'])) {
            $options['created_at'] = Mage::helper('catalog')->__('Date');
        }
        return $options;
    }
}
