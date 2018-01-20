<?php
/**
 * Fake layer class for price slider.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_Fake extends Mage_Catalog_Block_Layer_View
{
    /**
     * @var Mage_Catalog_Model_Layer
     */
    protected $_layer;

    /**
     * @var array
     */
    protected $_ignoredAttributes = array();

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->helper('layer')->isSearch()) {
            $this->_layer = Mage::getModel('catalogsearch/layer'); // Do not use singleton
        } else {
            $this->_layer = Mage::getModel('catalog/layer'); // Do not use singleton
        }
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
            ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
            ->setLayer($this->getLayer())
            ->init();

        $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            if ($this->hasIgnoredAttribute($attribute->getAttributeCode())) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = $this->_priceFilterBlockName;
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = $this->_decimalFilterBlockName;
            } else {
                $filterBlockName = $this->_attributeFilterBlockName;
            }

            $this->setChild(
                $attribute->getAttributeCode() . '_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                ->init()
            );
        }

        $this->getLayer()->apply();

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return $this->_layer;
    }

    /**
     * @return array
     */
    public function getIgnoredAttributes()
    {
        return $this->_ignoredAttributes;
    }

    /**
     * @param string $attrCode
     * @return array
     */
    public function addIgnoredAttribute($attrCode)
    {
        $this->_ignoredAttributes[] = $attrCode;

        return $this->_ignoredAttributes;
    }

    /**
     * @param string $attrCode
     * @return bool
     */
    public function hasIgnoredAttribute($attrCode)
    {
        return in_array($attrCode, $this->_ignoredAttributes);
    }
}