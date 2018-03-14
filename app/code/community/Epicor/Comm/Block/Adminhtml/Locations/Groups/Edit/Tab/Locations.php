<?php

class Epicor_Comm_Block_Adminhtml_Locations_Groups_Edit_Tab_Locations extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();
    protected $_group;

    public function __construct()
    {
        parent::__construct();
        $this->setId('grouplocationsGrid');
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
        return 'Locations';
    }

    public function getTabTitle()
    {
        return 'Locations';
    }

    public function isHidden()
    {
        return false;
    }
    
    /**
     * 
     * @return Epicor_Comm_Model_Location_Groupings
     */
    public function getGroup()
    {
        if (!$this->_group) {
            $this->_group = Mage::registry('group');
        }
        return $this->_group;
    }

    /**
     * 
     * @return type
     */
    protected function _prepareCollection()
    {
        $group = $this->getGroup();
        $joinCondition = "main_table.id = grouplocations.group_location_id and grouplocations.group_id = ".$group->getId();
        $collection = Mage::getModel('epicor_comm/location')->getCollection();
        $collection->getSelect()
                ->joinLeft(
                    array("grouplocations" => $collection->getTable('epicor_comm/location_grouplocations')),
                    $joinCondition,
                    array("position" => "grouplocations.position")
                );
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
        $this->addColumn('code', array(
            'header' => Mage::helper('epicor_comm')->__('Code'),
            'align' => 'left',
            'index' => 'code',
            'filter_index' => 'code'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name',
            'type' => 'text',
        ));
        $this->addColumn('address1', array(
            'header' => Mage::helper('epicor_comm')->__('Address 1'),
            'index' => 'address1',
            'filter_index' => 'address1',
            'type' => 'text',
        ));
        $this->addColumn('address2', array(
            'header' => Mage::helper('epicor_comm')->__('Address 2'),
            'index' => 'address2',
            'filter_index' => 'address2',
            'type' => 'text',
        ));
        $this->addColumn('address3', array(
            'header' => Mage::helper('epicor_comm')->__('Address 3'),
            'index' => 'address3',
            'filter_index' => 'address3',
            'type' => 'text',
        ));
        $this->addColumn('city', array(
            'header' => Mage::helper('epicor_comm')->__('City'),
            'index' => 'city',
            'filter_index' => 'city',
            'type' => 'text',
        ));
        $this->addColumn('county', array(
            'header' => Mage::helper('epicor_comm')->__('County'),
            'index' => 'county',
            'filter_index' => 'county',
            'type' => 'text',
        ));
        $this->addColumn('postcode', array(
            'header' => Mage::helper('epicor_comm')->__('Postcode'),
            'index' => 'postcode',
            'filter_index' => 'postcode',
            'type' => 'text',
        ));
        $this->addColumn('location_visible', array(
            'header' => Mage::helper('epicor_comm')->__('Location Visible'),
            'index' => 'location_visible',
            'filter_index' => 'location_visible',
            'type' => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
        ));
        $this->addColumn('row_id', array(
            'header' => Mage::helper('epicor_comm')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'width' => 0,
            'editable' => true
            
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getGroup()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_locationgroups/locationsgrid', $params);
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
        if (empty($this->_selected)) {
            $collection = Mage::getModel('epicor_comm/location_grouplocations')->getCollection();
            
            $group = $this->getGroup();

            $collection->addFieldToFilter('group_id', $group->getId());
            $items = $collection->getItems();
            $_locationPosition = array();
            foreach ($items as $location) {
                $_locationPosition[$location->getGroupLocationId()] = $location->getPosition();
                $this->_selected[$location->getGroupLocationId()] = array('id' => $location->getGroupLocationId());
            }
            Mage::unregister('location_position');
            Mage::register('location_position', $_locationPosition);
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
            $this->getCollection()->addFieldToFilter('main_table.id', array('in' => $ids));
        } else {
            $this->getCollection()->addFieldToFilter('main_table.id', array('nin' => $ids));
        }
    }

}
