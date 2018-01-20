<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 16:33
 */
class Interjar_LayeredNavSearch_Block_Catalog_Category_View_Layered_Navigation_Search_Form extends Mage_Core_Block_Template
{
    /**
     * Current Category from Registry
     *
     * @var Mage_Catalog_Model_Category $_category
     */
    protected $_category;

    /**
     * Layered Navigation Data Helper
     *
     * @var Interjar_LayeredNavSearch_Helper_Data $_helper
     */
    protected $_helper;

    /**
     * Interjar_LayeredNavSearch_Block_Catalog_Category_View_Layered_Navigation_Search_Form constructor
     */
    public function __construct()
    {
        $this->_category = Mage::registry('current_category');
        $this->_helper = Mage::helper('layerednavsearch');
    }

    /**
     * Return Bool on whether we can show the search bar in the nav for this category
     *
     * @return bool
     */
    public function canShowSearch()
    {
        if ($this->_helper->getSearchEnabled() && $this->_category) {
            return $this->_category->getSearchInNav();
        }
        return false;
    }

    /**
     * Return Current Search Term if set
     *
     * @return mixed
     */
    public function getCurrentSearchTerm()
    {
        return ($this->getRequest()->getParam('st')) ? $this->getRequest()->getParam('st') : '';
    }

    /**
     * Return URL for form postage
     *
     * @return mixed
     */
    public function getFormAction()
    {
        return Mage::getUrl('layeredsearch/search');
    }
}
