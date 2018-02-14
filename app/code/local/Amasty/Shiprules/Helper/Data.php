<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */ 
class Amasty_Shiprules_Helper_Data extends Amasty_Commonrules_Helper_Data
{
    protected $_counter;
    protected $_firstTime = true;

    public function getAllCarriers()
    {
        $carriers = array();
        foreach (Mage::getStoreConfig('carriers') as $code=>$config){
            if (!empty($config['title'])){
                $carriers[] = array('value'=>$code, 'label'=>$config['title']);
            }
        }  
        return $carriers;      
    }

    public function getCalculations()
    {
        $a = array(
            Amasty_Shiprules_Model_Rule::CALC_REPLACE  => $this->__('Replace'),
            Amasty_Shiprules_Model_Rule::CALC_ADD      => $this->__('Surcharge'),
            Amasty_Shiprules_Model_Rule::CALC_DEDUCT   => $this->__('Discount'),
        );
        return $a;       
    }

    public function getShippingPrice($block, $price, $flag)
    {
        $i = 0;
        $oldPrice = 0;
        $groups = method_exists($block, 'getEstimateRates') ? $block->getEstimateRates() : $block->getShippingRates();
        foreach ($groups as $group) {
            foreach ($group as $rate) {
                $oldPrice = $rate->getOldPrice();
                if ($i == $block->_counter) {
                    break 2;
                }
                $i++;
            }
        }
        $newPrice = $block->getQuote()->getStore()->convertPrice(
                Mage::helper('tax')->getShippingPrice($price, $flag, $block->getAddress()), $block->getQuote()->getCustomerTaxClassId()
            );

        if ($block->_firstTime) {
            $block->_counter++;
            $block->_firstTime = false;
        } else {
            $block->_firstTime = true;
        }
        if (Mage::getStoreConfig("amshiprules/discount/show_discount") && ($oldPrice > $price)) {
            $oldPrice = $block->getQuote()->getStore()->convertPrice(
                Mage::helper('tax')->getShippingPrice($oldPrice, $flag, $block->getAddress()), $block->getQuote()->getCustomerTaxClassId()
            );
            $newPrice = '<span style="' . Mage::getStoreConfig("amshiprules/discount/old_price_style") . '">' . $oldPrice . '</span>' . ' ' .
                '<span style="' . Mage::getStoreConfig("amshiprules/discount/new_price_style") . '">' . $newPrice . '</span>';
        }
        return $newPrice;
    }

    public function getPromotionsCartLink()
    {
        $promotionsCartUrl = Mage::helper('adminhtml')->getUrl('adminhtml/promo_quote');
        $link = '<a href="' . $promotionsCartUrl . '">Promotions / Shopping Cart Rules</a>';

        return $link;
    }
}