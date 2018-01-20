<?php

/**
 * Erp account master shoppers grid
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Mastershopper extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_erp_customer;
    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('masterShopper');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
         $this->setDefaultFilter(array('is_master' => 1));
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $collection = Mage::getResourceModel('customer/customer_collection');
            $collection->addFieldToFilter('erpaccount_id', $this->getErpCustomer()->getId());
            $collection->addAttributeToFilter('ecc_master_shopper', 1);
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            foreach ($collection->getAllIds() as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }

        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {

            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    public function getErpCustomer()
    {
        if (!$this->_erp_customer) {
            if (Mage::registry('customer_erp_account')) {
                $this->_erp_customer = Mage::registry('customer_erp_account');
            } else {
                $this->_erp_customer = Mage::getModel('epicor_comm/customer_erpaccount')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->_erp_customer;
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Master Shoppers';
    }

    public function getTabTitle()
    {
        return 'Master Shoppers';
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection');
        $collection->addNameToSelect();
        $collection->addFieldToFilter('erpaccount_id', $this->getErpCustomer()->getId());
        $collection->addAttributeToSelect('ecc_master_shopper');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'is_master') {

            $productIds = $this->_getSelected();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
                
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('is_master', array(
            'header' => Mage::helper('epicor_comm')->__('Select'),
            'align' => 'left',
            'type' => 'checkbox',
            'index' => 'entity_id',
            'name' => 'is_master',
            'values' => $this->_getSelected(),
            'sortable' => false,
            'field_name' => 'links[]',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Customer'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'name',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('epicor_comm')->__('Email'),
            'align' => 'left',
            'index' => 'email',
            'filter_index' => 'email'
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('epicor_comm')->__('Website'),
                'align' => 'left',
                'type' => 'options',
                'index' => 'website_id',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
            ));
        }


        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'ecc_master_shopper',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/mastershoppergrid', $params);
    }

}
