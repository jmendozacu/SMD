<?php

/**
 * List's  customer Grid config
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Customers_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('list_customers');
        $this->setIdColumn('id');
        $this->setSaveParametersInSession(false);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_customers' => 1));
    }

    protected function _prepareCollection()
    {

        $store_id = Mage::app()->getStore()->getStoreId();
        $erpAccount = Mage::helper('epicor_comm')->getErpAccountInfo();
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        $collection = $erpAccount->getCustomers($erpAccount->getId());
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection->addFieldToFilter('website_id', Mage::app()->getWebsite()->getId());
        $collection->addNameToSelect();
        /* filter start */
        $filter = $this->getParam($this->getVarNameFilter(), null);
        if (!is_null($filter)) {
            $filter = $this->helper('adminhtml')->prepareFilterString($filter);
            if (isset($filter['address_email_address'])) {
                $collection->addAttributeToFilter('email', array("like" => '%' . $filter['address_email_address'] . '%'));
            }
        }
        /* ends here */
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    protected function _toHtml()
    {
        $html = parent::_toHtml(false);
        return $html;
    }

    public function getRowUrl($row)
    {
        return '#';
    }

    protected function _getColumns()
    {
        $helper = Mage::helper('epicor_lists');
        $columns = array(
            'selected_customers' => array(
                'header' => 'Select',
                'header_css_class' => 'a-center',
                'index' => 'entity_id',
                'type' => 'checkbox',
                'name' => 'selected_customers',
                'values' => $this->_getSelected(),
                'align' => 'center',
                'filter_index' => 'main_table.entity_id',
                'sortable' => false,
                'field_name' => 'links[]'
            ),
            'name' => array(
                'header' => $helper->__('Name'),
                'index' => 'name',
                'type' => 'text',
                'condition' => 'LIKE',
            ),
            'address_email_address' => array(
                'header' => $helper->__('Email'),
                'index' => 'email',
                'type' => 'text',
            ),
            'row_id' => array(
                'header' => $helper->__('Position'),
                'name' => 'row_id',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'entity_id',
                'width' => 0,
                'editable' => true,
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display'
            ),
        );
        return $columns;
    }

    /**
     * Used in grid to return selected Products values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Sets the selected items array
     *
     * @param array $selected
     *
     * @return void
     */
    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('list_id'));
            }
        }
        return $this->list;
    }

    /**
     * Builds the array of selected customers
     * 
     * @return array
     */
    public function getSelected()
    {   
        if(!$this->getList()->getId()){
            $selectedCustomers = Mage::getSingleton('core/session')->getSelectedCustomers(true);
            foreach ($selectedCustomers as $customer) {
                $this->_selected[$customer] = array(
                    'id' => $customer
                );
            }
        }
        
        if (empty($this->_selected) && $this->getList()->getId()) {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */
            foreach ($list->getCustomers() as $customer) {
                $this->_selected[$customer->getId()] = array('id' => $customer->getId());
            }
        }
        return $this->_selected;
    }

    protected function _addColumnFilterToCollection($column)
    {

        if ($column->getId() == 'selected_customers') {
            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $ids));
            } else if ($ids) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $ids));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/customersgrid', array('_current' => true));
    }

}
