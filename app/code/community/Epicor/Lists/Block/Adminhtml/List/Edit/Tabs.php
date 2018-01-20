<?php

/**
 * List edit tabs
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * @var Epicor_Lists_Model_List
     */
    private $_list;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('form_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('List');
    }

    protected function _beforeToHtml()
    {
        $list = $this->getList();
        /* @var $list Epicor_Lists_Model_List */


        $detailsBlock = $this->getLayout()->createBlock('epicor_lists/adminhtml_list_edit_tab_details');

        $this->addTab('details', array(
            'label' => 'Details',
            'title' => 'Details',
            'content' => $detailsBlock->toHtml(),
        ));


        if ($list->getId()) {
            $typeInstance = $list->getTypeInstance();

//            if (Mage::app()->isSingleStoreMode() == false && $typeInstance->isSectionVisible('labels')) {
//                $this->addTab('labels', array(
//                    'label' => 'Labels',
//                    'title' => 'Labels',
//                    'url' => $this->getUrl('*/*/labels', array('id' => $list->getId(), '_current' => true)),
//                    'class' => 'ajax',
//                ));
//            }

            if ($typeInstance->isSectionVisible('erpaccounts')) {
                $this->addTab('erpaccounts', array(
                    'label' => 'ERP Accounts',
                    'title' => 'ERP Accounts',
                    'url' => $this->getUrl('*/*/erpaccounts', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ($typeInstance->isSectionVisible('brands')) {
                $this->addTab('brands', array(
                    'label' => 'Brands',
                    'title' => 'Brands',
                    'url' => $this->getUrl('*/*/brands', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }
            if ((Mage::app()->isSingleStoreMode() == false) && $typeInstance->isSectionVisible('websites')) {
                $this->addTab('websites', array(
                    'label' => 'Websites',
                    'title' => 'Websites',
                    'url' => $this->getUrl('*/*/websites', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ((Mage::app()->isSingleStoreMode() == false) && $typeInstance->isSectionVisible('stores')) {
                $this->addTab('stores', array(
                    'label' => 'Stores',
                    'title' => 'Stores',
                    'url' => $this->getUrl('*/*/stores', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ($typeInstance->isSectionVisible('customers')) {
                $this->addTab('customers', array(
                    'label' => 'Customers',
                    'title' => 'Customers',
                    'url' => $this->getUrl('*/*/customers', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ($typeInstance->isSectionVisible('products')) {
                $this->addTab('products', array(
                    'label' => 'Products',
                    'title' => 'Products',
                    'url' => $this->getUrl('*/*/products', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ($typeInstance->isSectionVisible('addresses')) {
                $this->addTab('addresses', array(
                    'label' => 'Addresses',
                    'title' => 'Addresses',
                    'url' => $this->getUrl('*/*/addresses', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }
            
            if ($list->getType() == 'Rp') {
            //if (1) {
                $this->addTab('restrictions', array(
                    'label' => 'Restrictions',
                    'title' => 'Restrictions',
                    'url' => $this->getUrl('*/*/restrictions', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }

            if ($typeInstance->isSectionVisible('messagelog')) {
                $this->addTab('messagelog', array(
                    'label' => 'Message Log',
                    'title' => 'Message Log',
                    'url' => $this->getUrl('*/*/messagelog', array('id' => $list->getId(), '_current' => true)),
                    'class' => 'ajax',
                ));
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * Gets the current List
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->_list) {
            $this->_list = Mage::registry('list');
        }
        return $this->_list;
    }

}
