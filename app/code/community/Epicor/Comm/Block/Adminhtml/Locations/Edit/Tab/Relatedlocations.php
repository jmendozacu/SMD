<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Relatedlocations extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('relatedlocationsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        
        $this->setDefaultFilter(array('selected_location' => 1));
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Related Locations';
    }

    public function getTabTitle()
    {
        return 'Related Locations';
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
        $locationId = $this->getLocation()->getId();
        $collection = Mage::getModel('epicor_comm/location')->getCollection()
                    ->addFieldToFilter('id', array('neq' => $locationId));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('selected_location', array(
            'header' => Mage::helper('epicor_comm')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_location',
            'values' => $this->_getSelected(),
            'filter_condition_callback' => array($this, '_filterSelectedCondition'),
            'align' => 'center',
            'index' => 'id',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('epicor_comm')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        $this->addColumn('relatedlocation_code', array(
            'header' => Mage::helper('epicor_comm')->__('Code'),
            'align' => 'left',
            'index' => 'code',
            'filter_index' => 'code'
        ));

        $this->addColumn('relatedlocation_name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_address1', array(
            'header' => Mage::helper('epicor_comm')->__('Address 1'),
            'index' => 'address1',
            'filter_index' => 'address1',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_address2', array(
            'header' => Mage::helper('epicor_comm')->__('Address 2'),
            'index' => 'address2',
            'filter_index' => 'address2',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_address3', array(
            'header' => Mage::helper('epicor_comm')->__('Address 3'),
            'index' => 'address3',
            'filter_index' => 'address3',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_city', array(
            'header' => Mage::helper('epicor_comm')->__('City'),
            'index' => 'city',
            'filter_index' => 'city',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_county', array(
            'header' => Mage::helper('epicor_comm')->__('County'),
            'index' => 'county',
            'filter_index' => 'county',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_postcode', array(
            'header' => Mage::helper('epicor_comm')->__('Postcode'),
            'index' => 'postcode',
            'filter_index' => 'postcode',
            'type' => 'text',
        ));
        $this->addColumn('relatedlocation_location_visible', array(
            'header' => Mage::helper('epicor_comm')->__('Location Visible'),
            'index' => 'location_visible',
            'filter_index' => 'location_visible',
            'type' => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getLocation()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_locations/relatedlocationsgrid', $params);
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
            $collection = Mage::getModel('epicor_comm/location_relatedlocations')->getCollection();
            
            $location = $this->getLocation();

            $collection->addFieldToFilter('location_id', $location->getId());
            $items = $collection->getItems();
            foreach ($items as $relatedLocation) {
                $this->_selected[$relatedLocation->getRelatedLocationId()] = array('id' => $relatedLocation->getRelatedLocationId());
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
            $this->getCollection()->addFieldToFilter('id', array('in' => $ids));
        } else {
            $this->getCollection()->addFieldToFilter('id', array('nin' => $ids));
        }
    }

}
