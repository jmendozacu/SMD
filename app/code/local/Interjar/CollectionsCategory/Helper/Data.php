<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 31/01/2018
 * Time: 11:48
 */
class Interjar_CollectionsCategory_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return Update Products URL
     *
     * @return string
     */
    public function getProductsUpdateUrl()
    {
        return Mage::getUrl('collections/update/products');
    }
}
