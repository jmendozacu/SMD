<?php
/**
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $isSearch = $this->helper('layer/view')->isSearch();
        $isEnterprise = Mage::helper('core')->isModuleEnabled('Enterprise_Search');

        if ($isEnterprise) {
            $left = $isSearch ? 'enterprisesearch.leftnav' : 'enterprisecatalog.leftnav';
        } else {
            $left = $isSearch ? 'catalogsearch.leftnav' : 'catalog.leftnav';
        }

        if ($this->getNameInLayout() != $left) {
            $block = $this->getLayout()->getBlock($left);
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

    /**
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return $this->helper('layer/view')->getLayer();
    }
}