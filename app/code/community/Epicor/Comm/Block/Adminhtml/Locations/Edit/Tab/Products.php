<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('sku');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        
        $this->setDefaultFilter(array('selected_product' => 1));
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Products';
    }

    public function getTabTitle()
    {
        return 'Products';
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * 
     * @return Epicor_Comm_Model_Location
     */
    public function getLocation()
    {
        if (!$this->_location) {
            $this->_location = Mage::registry('location');
        }
        return $this->_location;
    }

    /**
     * 
     * @return type
     */
    protected function _prepareCollection()
    {
//        $collection = Mage::getModel('epicor_comm/location_product')->getCollection();
//        /* @var $collection Epicor_Comm_Model_Mysql4_Location_Product_Collection */
//
        $locationCode = $this->getLocation()->getCode();
//
//        $collection->addFieldToFilter('main_table.location_code', $location->getCode());
//        $collection->joinExtraProductInfo();

        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $collection->addAttributeToSelect('name');

        $table = $collection->getTable('epicor_comm/location_product');
        $locationCode = Mage::getSingleton('core/resource')->getConnection('default_write')->quote($locationCode);
        $collection->getSelect()->joinLeft(array('loc' => $table), 'loc.product_id=e.entity_id AND loc.location_code=' . $locationCode . '', array('*'), null, 'left');
        $collection->getSelect()->group('e.entity_id');
        $collection->addAttributeToSelect('uom');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('selected_product', array(
            'header' => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_product',
            'values' => $this->_getSelected(),
            'filter_condition_callback' => array($this, '_filterSelectedCondition'),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('epicor_comm')->__('SKU'),
            'align' => 'left',
            'index' => 'sku',
            'filter_index' => 'sku',
            'column_css_class' => 'col-sku',
            'width' => '200px',
        ));

        $this->addColumn('uom', array(
            'header' => Mage::helper('epicor_comm')->__('UOM'),
            'index' => 'uom',
            'filter_index' => 'uom',
            'type' => 'text',
            'sortable' => true,
            'filterable' => true,
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('epicor_comm')->__('Product Name'),
            'align' => 'left',
            'index' => 'name',
            'column_css_class' => 'col-product_name',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getLocation()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_locations/productsgrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $collection = Mage::getModel('epicor_comm/location_product')->getCollection();
            /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

            $location = $this->getLocation();

            $collection->addFieldToFilter('location_code', $location->getCode());

            foreach ($collection->getItems() as $location_product) {
                $this->_selected[$location_product->getProductId()] = array('entity_id' => $location_product->getProductId());
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('entity_id' => $id);
            }
        }
    }

    /**
     * 
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param Varien_Object $column
     */
    protected function _filterSelectedCondition($collection, $column)
    {
        if ($column->getFilter()->getValue() === null) {
            return;
        }
        $ids = $this->_getSelected();
        if (empty($ids)) {
            $ids[] = 0;
        }
        if ($column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter('entity_id', array('in' => $ids));
        } else {
            $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $ids));
        }
    }

}
