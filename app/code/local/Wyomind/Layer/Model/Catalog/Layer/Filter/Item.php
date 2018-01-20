<?php
/**
 * Layer filter item rewrite.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * @var string
     */
    protected $_optionsSeparator;

    /**
     * Define options separator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_optionsSeparator = Wyomind_Layer_Helper_Data::OPTIONS_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $value = $this->getValue();
        if ($this->_helper()->isFilterMultiple($this->getFilter())) {
            $values = $this->getCurrentValues();
            $values[] = $value;
            $value = implode($this->_optionsSeparator, array_unique($values));
        }
        $query = array(
            $this->getFilter()->getRequestVar() => $value,
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        return $this->_helper()->buildUrl($query);
    }

    /**
     * @return string
     */
    public function getRemoveUrl()
    {
        $resetValue = $this->getFilter()->getResetValue();
        if ($this->_helper()->isFilterMultiple($this->getFilter())) {
            $values = $this->getCurrentValues();
            $value = $this->getValue();
            if (false !== ($k = array_search($value, $values))) {
                unset($values[$k]);
            }
            if (!empty($values)) {
                $resetValue = implode($this->_optionsSeparator, array_unique($values));
            }
        }
        $query = array($this->getFilter()->getRequestVar() => $resetValue);

        return $this->_helper()->buildUrl($query);
    }

    /**
     * @return array
     */
    public function getCurrentValues()
    {
        $values = array();
        $param = Mage::app()->getRequest()->getParam($this->getFilter()->getRequestVar());
        if (null !== $param && '' !== $param) {
            $values = explode($this->_optionsSeparator, $param);
        }

        return $values;
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        $values = Mage::app()->getRequest()->getParam($this->getFilter()->getRequestVar());
        $value = $this->getValue();
        if (!is_null($values) && in_array($value, explode($this->_optionsSeparator, $values))) {
            return true;
        }

        return false;
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setValue($value)
    {
        $this->setOriginalValue($value);
        if (is_numeric($value) && $this->_helper()->isSeoEnabled()) {
            if ($this->_helper()->isAttributeFilter($this->getFilter())) {
                $attrCode = $this->getFilter()->getAttributeModel()->getAttributeCode();
                $value = $this->_helper()->getOptionKey($attrCode, $value);
            } elseif ($this->_helper()->isCategoryFilter($this->getFilter())) {
                $value = $this->_helper()->getCategoryKey($value);
            }
        }

        return $this->setData('value', $value);
    }

    /**
     * Compatibility with Wyomind Attribute Option Pro
     *
     * @return bool
     */
    public function getIcon()
    {
        $img = false;
        if (Mage::helper('core')->isModuleEnabled('Wyomind_AttributeOptionPro') &&
            $this->_helper()->isAttributeFilter($this->getFilter()))
        {
            $img = Mage::helper('aop')->getAttributeOptionImage($this->getOriginalValue());
        }

        return $img;
    }

    /**
     * @return Wyomind_Layer_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('layer');
    }
}