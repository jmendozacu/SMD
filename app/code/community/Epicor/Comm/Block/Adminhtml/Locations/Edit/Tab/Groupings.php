<?php

/**
 * Location Groupings Grid
 *
 */
class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Groupings extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_selected = array();
    public function __construct()
    {
        parent::__construct();
        $this->setId('groupings');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        
        $this->setDefaultFilter(array('selected_group' => 1));        
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
    
    public function getTabTitle()
    {
        return 'Groupings';
    }
    
    public function getTabLabel()
    {
        return 'Groupings';
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    protected function _getLocation()
    {
        if (!$this->_location) {
            $this->_location = Mage::registry('location');
        }
        return $this->_location;
    }

    protected function _prepareCollection()
    {
        $id = $this->_getLocation()->getId();
        $collection = Mage::getModel('epicor_comm/location_groupings')->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $editUrl = 'adminhtml/epicorcomm_locations/editgroup/id/'.$this->_getLocation()->getId();
        
        $this->addColumn('selected_group', array(
            'header' => Mage::helper('epicor_comm')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_group',
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
        
        $this->addColumn('group_name', array(
            'header' => Mage::helper('epicor_comm')->__('Group Name'),
            'align' => 'center',
            'index' => 'group_name',
            'filter_index' => 'group_name'
        ));

        $this->addColumn('group_expandable', array(
            'header' => Mage::helper('epicor_comm')->__('Group Expandable'),
            'align' => 'left',
            'index' => 'group_expandable',
            'type'  => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'filter_index' => 'group_expandable'
        ));
        $this->addColumn('enabled', array(
            'header' => Mage::helper('epicor_comm')->__('Enabled'),
            'align' => 'left',
            'index' => 'enabled',
            'type'  => 'options',
            'options'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'filter_index' => 'enabled'
        ));
        
        $this->addColumn('rowdata', array(
            'header' => Mage::helper('flexitheme')->__('Row'),
            'align' => 'left',
            'name' => 'rowdata',
            'width' => 0,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->_getLocation()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_locations/groupingsgrid', $params);
    }

    public function getSkipGenerateContent()
    {
        return false;
    }

    public function getTabClass()
    {
        return 'ajax notloaded';
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected)) {
            $id = $this->_getLocation()->getId();
            $collection = Mage::getModel('epicor_comm/location_grouplocations')->getCollection();
            $collection->addFieldToFilter('group_location_id', $id);
            $items = $collection->getItems();
            foreach ($items as $selectedGroup) {
                
                $this->_selected[$selectedGroup->getGroupId()] = array('id' => $selectedGroup->getGroupId());
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
