<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalogsearch_Layer extends Mage_CatalogSearch_Block_Layer
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->getNameInLayout() != 'catalogsearch.leftnav') {
            $block = $this->getLayout()->getBlock('catalogsearch.leftnav');
            if ($block) {
                $this->_children = $block->getChild();

                return $this;
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->helper('layer/view')->getFilters($this);
    }
}