<?php
/**
 * Product price block override
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Common_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price
{
    /**
     * Get tier prices (formatted)
     * 
     * Only difference to parent is the 'savePercent' can now be rounded to precision
     * rather than just rounding it up
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */    
    public function getTierPrices($product = null, $parent = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $prices = $product->getFormatedTierPrice();
        $version = Mage::getVersionInfo();
        if($version['minor'] >= 9) {
            // if our parent is a bundle, then we need to further adjust our tier prices
            if (isset($parent) && $parent->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                /* @var $bundlePriceModel Mage_Bundle_Model_Product_Price */
                $bundlePriceModel = Mage::getModel('bundle/product_price');
            }
        }

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $productPrice = $product->getPrice();
                if ($product->getPrice() != $product->getFinalPrice()) {
                    $productPrice = $product->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $product->getGroupPrice();
                if ($productPrice > $groupPrice) {
                    $productPrice = $groupPrice;
                }

                if ($price['price'] < $productPrice) {
                    // use the original prices to determine the percent savings
                    //$price['savePercent'] = ceil(100 - ((100 / $productPrice) * $price['price']));
                    $precision = Mage::getStoreConfig('epicor_common/tier_prices/precision');
                    $price['savePercent'] = round(100 - ((100 / $productPrice) * $price['price']),$precision);

                    // if applicable, adjust the tier prices
                    if (isset($bundlePriceModel)) {
                        $price['price']         = $bundlePriceModel->getLowestPrice($parent, $price['price']);
                        $price['website_price'] = $bundlePriceModel->getLowestPrice($parent, $price['website_price']);
                    }

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($product, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('tax')->getPrice($product, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('catalog')->canApplyMsrp($product)) {
                        $oldPrice = $product->getFinalPrice();
                        $product->setPriceCalculation(false);
                        $product->setPrice($tierPrice);
                        $product->setFinalPrice($tierPrice);

                        $this->getLayout()->getBlock('product.info')->getPriceHtml($product);
                        $product->setPriceCalculation(true);

                        $price['real_price_html'] = $product->getRealPriceHtml();
                        $product->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }
    
    public function callParentToHtml()
    {
        return parent::callParentToHtml();
    }
}
