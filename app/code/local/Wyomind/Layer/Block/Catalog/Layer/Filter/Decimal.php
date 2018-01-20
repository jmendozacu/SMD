<?php
/**
 * Layer filter decimal block rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Decimal
{
    /**
     * @var array
     */
    protected $_values;

    /**
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getMinMaxValues()
    {
        if (null === $this->_values) {
            // Instantiate block directly so we can remove price filter
            $block = new Wyomind_Layer_Block_Catalog_Layer_Fake();
            $block->addIgnoredAttribute(
                $this->_filter->getAttributeModel()->getAttributeCode()
            );
            $block->setLayout($this->getLayout()); // will apply filters
            $collection = $block->getLayer()->getProductCollection();
            $attrCode = $this->_filter->getAttributeModel()->getAttributeCode();
            $collection->joinAttribute(
                $attrCode,
                'catalog_product/' . $attrCode,
                'entity_id',
                null,
                'left',
                $this->_filter->getStoreId()
            );
            $this->_values = Mage::getResourceModel('layer/catalog_layer_filter_decimal')
                ->getMinMaxValues($this->_filter, $collection, $collection->getAllIds());
        }

        return $this->_values;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        $values = $this->getMinMaxValues();

        return $values[0];
    }

    /**
     * @return float
     */
    public function getMax()
    {
        $values = $this->getMinMaxValues();

        return $values[1];
    }

    /**
     * @return array
     */
    public function getValues()
    {
        $min = $this->getMin();
        $max = $this->getMax();
        $values = array($min, $max);
        $interval = $this->_filter->getInterval();
        if ($interval) {
            $values = array(
                (int) max($min, $interval[0] ? $interval[0] : ''),
                (int) min($max, $interval[1] ? $interval[1] : $max)
            );
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
     * @return int
     */
    public function getItemsCount()
    {
        if ($this->helper('layer')->isPriceSliderEnabled() && $this->getMin() >= $this->getMax()) {
            return 0;
        }

        return parent::getItemsCount();
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return $this->_filter->getRequestVar();
    }

    /**
     * @return Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function getFilter()
    {
        return $this->_filter;
    }
}