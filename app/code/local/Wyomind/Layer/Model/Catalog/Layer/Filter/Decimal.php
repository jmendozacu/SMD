<?php
/**
 * Layer filter decimal rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{
    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!$this->_helper()->isPriceSliderEnabled()) {
            parent::apply($request, $filterBlock);
        } else {
            /**
             * Filter must be string: $index, $range
             */
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter) {
                return $this;
            }

            $filter = explode('-', $filter);
            if (count($filter) != 2) {
                return $this;
            }

            list($from, $to) = $filter;

            $this->setInterval(array($from, $to));

            Mage::getResourceModel('layer/catalog_layer_filter_decimal')
                ->applyFilterToCollection($this, $from, $to);

            $this->_helper()->addAppliedFilter($this->getAttributeCode());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestVar()
    {
        return $this->_helper()->getAttributeRequestVar($this->getAttributeCode(), $this->getStoreId());
    }

    /**
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getAttributeModel()->getAttributeCode();
    }

    /**
     * @return array
     */
    protected function _getItemsData()
    {
        if (!$this->_helper()->canShowFilter($this)) {
            return array();
        }

        return parent::_getItemsData();
    }

    /**
     * @return Wyomind_Layer_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('layer');
    }
}