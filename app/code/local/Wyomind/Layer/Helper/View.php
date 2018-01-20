<?php
/**
 * Layer view helper.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Helper_View extends Wyomind_Layer_Helper_Data
{
    /**
     * @var array
     */
    protected $_filterableAttributes;

    /**
     * @param Mage_Core_Block_Abstract $block
     * @param mixed $filter
     * @return bool
     */
    public function canShowBlockFilter($block, $filter)
    {
        switch ($block->getNameInLayout()) {
            case 'wyomind.layer.top':
                $show = $this->isTopFilter($filter);
                break;
            case 'wyomind.layer.right':
                $show = $this->isRightFilter($filter);
                break;
            default:
                $show = $this->isLeftFilter($filter);
        }

        return $show;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        if (null === $this->_filterableAttributes) {
            $this->_filterableAttributes = $this->getLayer()->getFilterableAttributes();
        }

        return $this->_filterableAttributes;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @return array
     */
    public function getFilters($block)
    {
        $filters = array();

        $categoryFilter = $block->getChild('category_filter');
        if ($categoryFilter && $this->canShowBlockFilter($block, $categoryFilter)) {
            $filters[] = $categoryFilter;
        }

        $filterableAttributes = $this->getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            $filter = $block->getChild($attribute->getAttributeCode() . '_filter');
            if ($filter && $this->canShowBlockFilter($block, $filter)) {
                $filters[] = $filter;
            }
        }

        return $filters;
    }
}