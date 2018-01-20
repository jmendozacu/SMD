<?php

/**
 * List Addresses Serialized Grid Frontend
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Shipto_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('addressesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }

    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('contract'));
        return $this->list;
    }

    /**
     * Build data for List Addresses
     *
     */
    protected function _prepareCollection()
    {
        $customerAddress = Mage::getModel('epicor_comm/customer_address');
        /* @var $customerAddress Epicor_Comm_Model_Customer_Address */

        $collection = $customerAddress->getCustomerAddressesCollection();
        /* @var $collection Mage_Customer_Model_Entity_Address_Collection */

        $sessionHelper = Mage::helper('epicor_lists/session');
        /* @var $sessionHelper Epicor_Lists_Helper_Session */

        $filter = $sessionHelper->getValue('ecc_shipto_select_filter');
        if ($filter) {
            $collection->addAttributeToFilter('erp_address_code', array('in' => $filter));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Addresses
     *
     *
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'erp_address_code', array(
            'header' => $this->__('Address Code'),
            'index' => 'erp_address_code',
            'filter_index' => 'erp_address_code',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'full_name', array(
            'header' => $this->__('Name'),
            'index' => 'full_name',
            'filter_index' => 'full_name',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'flatt_address', array(
            'header' => $this->__('Address'),
            'index' => 'flat_address',
            'filter_index' => 'flatt_address',
            'type' => 'text',
            'renderer' => new Epicor_Lists_Block_Contract_Renderer_Address(),
            'filter_condition_callback' => array($this, '_addressFilter'),
            )
        );

        $this->addColumn(
            'email', array(
            'header' => $this->__('Email'),
            'index' => 'email',
            'filter_index' => 'email',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'select', array(
            'header' => $this->__('Select'),
            'width' => '280',
            'index' => 'erp_address_code',
            'renderer' => 'epicor_lists/contract_shipto_grid_renderer_select',
            'links' => 'true',
            'getter' => 'getErpAddressCode',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Select'),
                    'url' => array('base' => '*/*/selectShipto'),
                    'field' => 'shipto'
                ),
            )
            )
        );


        return parent::_prepareColumns();
    }

    protected function _addressFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $clone = clone $collection;

        $filterIds = array();
        foreach ($clone->getItems() as $item) {
            /* @var $item Epicor_Lists_Model_List */
            if (stripos(Mage::helper('epicor_comm')->getFlattenedAddress($item), $value) !== false) {
                $filterIds[] = $item->getId();
            }
        }

        $collection->addFieldToFilter('entity_id', array('in' => $filterIds));
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/shiptogrid', array('_current' => true));
    }

}
