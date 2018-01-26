<?php

/**
 * Created by PhpStorm.
 * User: song
 * Date: 16-8-3
 * Time: 下午4:24
 */
class Silk_Retailer_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function accessRetailerStep()
    {
        // 1. If customer is using a B2C account and they have "order samples" in their shopping cart, the Select Retailers section will appear on checkout.
        // 2. If customer is using a B2C account and they DO NOT have "order samples" in their chopping, the Select Retailers section will NOT appear on checkout.
        // 3. If the customer is using a B2B account, the select retailers section will NOT appear on checkout.
        $access = false;

        // first we must exist retailer demo data
        $record = Mage::getSingleton('silk_retailer/retailer')->getCollection()
                    ->AddFieldToFilter('status', 1)
                    ->getSize();
        if ($record) {
            if ($this->isB2C()) {
                // if shopping cart exist sample product
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $items = $quote->getAllVisibleItems();
                foreach ($items as $item) {
                    $access = $this->isSampleProduct($item->getProduct());
                    if ($access) {
                        // if exist old retailer id, remove it
                        $quote->setReatiler(null)
                            ->setRetailerId(null);
                        break;
                    }
                }
            }
        }

        return $access;
    }

    public function isB2B()
    {
        return Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2B';
    }

    public function isB2C()
    {
        return (Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'B2C') || (Mage::helper('epicor_comm')->getErpAccountInfo()->getAccountType() == 'Guest');
    }

    public function allowAddSampleProduct()
    {
        $currentCount = $this->getSampleProductCount();
        $historyCount = Mage::getModel('silk_retailer/record')->getHistorySampleSum();
        $allow = ($this->getSampleTotalMax() > ($currentCount + $historyCount));
        return $allow;
    }

    public function isSampleProduct($product)
    {
        return (boolean)Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'is_sample', Mage::app()->getStore()->getId());
    }

    /**
     * if current qty lt max number return current qty
     *
     * @param $qty
     * @return allowed
     */
    public function limitSampleProductQty($item)
    {
        $currentCount = $this->getSampleProductCount();
        $historyNum = Mage::getModel('silk_retailer/record')->getHistorySampleSum();
        $qty = $item->getQty();
        if ($this->isB2B()) {
            $allowTotalMax = (int)Mage::getStoreConfig('sales/sample_product_b2b/total_qty');
            $allowEachMax = (int)Mage::getStoreConfig('sales/sample_product_b2b/each_qty');
            $qty = $this->getAllowQty($qty, $currentCount, $allowTotalMax, $allowEachMax, $historyNum);
        } elseif ($this->isB2C()) {
            $allowTotalMax = (int)Mage::getStoreConfig('sales/sample_product_b2c/total_qty');
            $allowEachMax = (int)Mage::getStoreConfig('sales/sample_product_b2c/each_qty');
            $qty = $this->getAllowQty($qty, $currentCount, $allowTotalMax, $allowEachMax, $historyNum);
        }
        $item->setQty($qty);
        return $item;
    }

    public function getAllowQty($qty, $currentCount, $allowTotalMax, $allowEachMax, $historyNum)
    {
        $allowTotalMax = $allowTotalMax - $historyNum;
        $allow = $allowTotalMax - $currentCount;

        // if $allow lt 0
        if ($allow) {
            $qty = $qty > $allow ? $allow : $qty ;
            $qty = $qty > $allowEachMax ? $allowEachMax : $qty ;
        } else {
            $qty = 0;
        }
        return $qty;
    }

    public function getSampleEachMax()
    {
        if ($this->isB2B()) {
            return (int)Mage::getStoreConfig('sales/sample_product_b2b/each_qty');
        } elseif ($this->isB2C()) {
            return (int)Mage::getStoreConfig('sales/sample_product_b2c/each_qty');
        }
        return null;
    }

    public function getSampleTotalMax()
    {
        if ($this->isB2B()) {
            return (int)Mage::getStoreConfig('sales/sample_product_b2b/total_qty');
        } elseif ($this->isB2C()) {
            return (int)Mage::getStoreConfig('sales/sample_product_b2c/total_qty');
        }
        return null;
    }

    public function validateSampleNumber($product, $qty)
    {
        $allowTotalMax = 0;
        $allowEachMax = 0;
        // history num have contain current sample product num
//        $currentCount = $this->getSampleProductCount();
        $item = Mage::getSingleton('checkout/session')->getQuote()->getItemByProduct($product);
        if ($item) {
            $productQty = $item->getQty();
        } else {
            $productQty = 0;
        }

        $qty = $qty + $productQty;
        $currentCount = 0;
        $historyNum = Mage::getModel('silk_retailer/record')->getHistorySampleSum();
        $currentCount = $qty + $historyNum + $currentCount;

        if ($this->isB2B()) {
            $allowTotalMax = Mage::getStoreConfig('sales/sample_product_b2b/total_qty');
            $allowEachMax = Mage::getStoreConfig('sales/sample_product_b2b/each_qty');
        } elseif ($this->isB2C()) {
            $allowTotalMax = Mage::getStoreConfig('sales/sample_product_b2c/total_qty');
            $allowEachMax = Mage::getStoreConfig('sales/sample_product_b2c/each_qty');
        }

        // is each
        if ($qty > $allowEachMax) {
            return false;
        }

        if ($currentCount > $allowTotalMax) {
            return false;
        }

        return true;
    }

    public function getSampleErrorMsg()
    {
        if ($this->isB2B()) {
            return Mage::getStoreConfig('sales/sample_product_b2b/message');
        } elseif ($this->isB2C()) {
            return Mage::getStoreConfig('sales/sample_product_b2c/message');
        }
    }

    public function getSampleProductExpiredTime()
    {
        if ($this->isB2B()) {
            return Mage::getStoreConfig('sales/sample_product_b2b/keep_day');
        } elseif ($this->isB2C()) {
            return Mage::getStoreConfig('sales/sample_product_b2c/keep_day');
        }
    }

    public function getSampleProductCount()
    {
        $count = 0;
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            if ($this->isSampleProduct($item->getProduct())) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * allow sample product number, it contain current quote sample product
     * @return int|null
     */
    public function getAllowSampleNumber()
    {
        $currentCount = Mage::getModel('silk_retailer/record')->getHistorySampleSum();
        $allowMax = $this->getSampleTotalMax();
        $allow = $allowMax - $currentCount;
        return $allow < 0 ? 0 : $allow ;
    }

    public function recordOrDeleteSampleProductRecord($product)
    {
        // If is not B2B and is sample product
        // we need record buy sample product
        if ($this->isSampleProduct($product)) {
            return true;
        }
        return false;
    }

    public function allowBuy()
    {
        if ($this->isB2C()) {
            return (boolean)Mage::getStoreConfig('sales/sample_product_b2c/buy_other_product');
        }
        return true;
    }

    public function getRefusedMessage()
    {
        return Mage::getStoreConfig('sales/sample_product_b2c/refused_message');
    }

    /**
     * get retailer hash value
     *
     * @param $city
     * @param $state
     * @param $zip
     * @param $town
     * @return int
     */
    public function getHash($city, $state, $zip, $town)
    {
        $tagData = array(
            strtolower(trim($city)),
            strtolower(trim($state)),
            strtolower(trim($zip)),
            strtolower(trim($town))
        );

        $tagData = implode('', $tagData);

        $hash = 0;
        $s    = md5($tagData);
        $seed = 5;
        $len  = 32;
        for ($i = 0; $i < $len; $i++) {
            $hash = ($hash << $seed) + $hash + ord($s{$i});
        }

        return $hash & 0x7FFFFFFF;
    }

    public function getProductConfiguratorAutoFillFieldId()
    {
        return Mage::getStoreConfig('sales/silk_product_configurator_settings/auto_fill_field_id');;
    }
}
