<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sortby
 *
 * @author Paul.Ketelle
 */
class Epicor_QuickOrderPad_Block_Catalog_Product_List_Sortby extends Mage_Core_Block_Template
{

    //put your code here

    public function getCurrentSortBy()
    {
        $sort_by = Mage::app()->getRequest()->getParam('sort_by', Mage::getSingleton('catalog/session')->getQopSortBy()) ? : 'uom';
        Mage::getSingleton('catalog/session')->setQopSortBy($sort_by);
        
        return $sort_by;
    }

    public function getSortByUrl($sort_by)
    {
        return $this->getUrl('*/*/*', array(
                    '_query' => array('sort_by' => $sort_by),
                    '_current' => true,
                    '_escape' => true,
                    '_use_rewrite' => true
                        )
        );
    }

    public function getOrigPager()
    {
        if ($this->getParentBlock() instanceof Varien_Object) {
            $pagerBlock = $this->getParentBlock()->getChild('product_list_toolbar_pager-orig');
            if ($pagerBlock instanceof Varien_Object) {

                /* @var $pagerBlock Mage_Page_Block_Html_Pager */
                $pagerBlock->setAvailableLimit($this->getAvailableLimit());

                $pagerBlock->setUseContainer($this->getUseContainer())
                        ->setShowPerPage($this->getShowPerPage())
                        ->setShowAmounts($this->getShowAmounts())
                        ->setLimitVarName($this->getLimitVarName())
                        ->setPageVarName($this->getPageVarName())
                        ->setLimit($this->getLimit())
                        ->setFrameLength($this->getFrameLength())
                        ->setJump($this->getJump())
                        ->setCollection($this->getCollection());

                return $pagerBlock->toHtml();
            }
        }
    }

}
