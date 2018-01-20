<?php
/**
 * Layer filter price block rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{
    /**
     * @var array
     */
    protected $_prices;

    /**
     * @return array
     */
    public function getMinMaxPrices()
    {
        if (null === $this->_prices) {
            // Instantiate block directly so we can remove price filter
            $block = new Wyomind_Layer_Block_Catalog_Layer_Fake();
            $block->addIgnoredAttribute(
                $this->_filter->getAttributeModel()->getAttributeCode()
            );
            $block->setLayout($this->getLayout()); // will apply filters
            $collection = $block->getLayer()->getProductCollection();
            $this->_prices = Mage::getResourceModel('layer/catalog_layer_filter_price')
                ->getMinMaxPrices($collection, $collection->getAllIds());
        }

        return $this->_prices;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        $prices = $this->getMinMaxPrices();

        $from = $this->getBaseCurrency()->getCode();
        $to = $this->getCurrentCurrency()->getCode();
        $price = Mage::helper('directory')->currencyConvert($prices[0], $from, $to);

        return $price;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        $prices = $this->getMinMaxPrices();

        $from = $this->getBaseCurrency()->getCode();
        $to = $this->getCurrentCurrency()->getCode();
        $price = Mage::helper('directory')->currencyConvert($prices[1], $from, $to);

        return $price;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $min = $this->getMin();
        $max = $this->getMax();
        $values = array($min, $max);
        foreach ($this->getLayer()->getState()->getFilters() as $filter) {
            if ($filter->getFilter() instanceof Mage_Catalog_Model_Layer_Filter_Price) {
                if ($filter->getFilter()->getInterval()) {
                    $interval = $filter->getFilter()->getInterval();
                    $values = array(
                        (int) max($min, $interval[0] ? $interval[0] : ''),
                        (int) min($max, $interval[1] ? $interval[1] : $max));
                }
            }
        }

        return $values;
    }

    /**
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrentCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrency();
    }

    /**
     * @return Mage_Directory_Model_Currency
     * @throws Mage_Core_Exception
     */
    public function getBaseCurrency()
    {
        return Mage::app()->getWebsite()->getBaseCurrency();
    }

    /**
     * @return int
     */
    public function getItemsCount()
    {
        $values = $this->getValues();
        if ($this->getMin() >= $this->getMax() || $values[0] >= $values[1]) {
            return 0;
        }

        return parent::getItemsCount();
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return $this->helper('layer')->getPriceRequestVar();
    }

    /**
     * @return Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function getFilter()
    {
        return $this->_filter;
    }
}