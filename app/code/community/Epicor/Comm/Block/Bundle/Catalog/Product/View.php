<?php

/**
 * Bundle product view override
 * 
 * To cope with teir prices being fixed for fixed price bundles
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Bundle_Catalog_Product_View extends Mage_Bundle_Block_Catalog_Product_View
{

    /**
     * Get tier prices (formatted)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getTierPrices($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices = $product->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            if ($product->getPriceType() == 1) {
                foreach ($prices as $price) {
                    $price['price_qty'] = $price['price_qty'] * 1;

                    $_productPrice = $product->getPrice();
                    if ($_productPrice != $product->getFinalPrice()) {
                        $_productPrice = $product->getFinalPrice();
                    }
                    // Group price must be used for percent calculation if it is lower
                    $groupPrice = $product->getGroupPrice();
                    if ($_productPrice > $groupPrice && $groupPrice != 0) {
                        $_productPrice = $groupPrice;
                    }

                    $precision = Mage::getStoreConfig('epicor_common/tier_prices/precision');
                    $price['savePercent'] = round(100 - ((100 / $_productPrice) * $price['price']), $precision);

                    $price['formated_price'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'])));
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'], true)));
                    $res[] = $price;
                }
            } else {
                foreach ($prices as $price) {
                    $price['price_qty'] = $price['price_qty'] * 1;
                    $price['savePercent'] = ceil(100 - $price['price']);
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'])));
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(Mage::app()->getStore()->convertPrice(Mage::helper('tax')->getPrice($product, $price['website_price'], true)));
                    $res[] = $price;
                }
            }
        }

        return $res;
    }

}
