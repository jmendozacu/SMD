<?php

class Epicor_Common_Block_Adminhtml_Access_Right_Edit_Tab_Groups extends Mage_Adminhtml_Block_Widget_Grid {

    private $_selected = array();

    public function _construct() {
        $this->setId('accessRightGroupsGrid');
        $this->setDefaultSort('entity_name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->getRight()->getId()) {
            $this->setDefaultFilter(array('group_in_right' => 1));
        }
    }

    /**
     * 
     * @return Epicor_Common_Model_Access_Right
     */
    public function getRight() {

        if (!$this->_accessright) {
            $this->_accessright = Mage::registry('access_right_data');
        }
        return $this->_accessright;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_common/access_group')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('group_in_right', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'group[]',
            'name' => 'group',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'editable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('group_name', array(
            'header' => Mage::helper('epicor_common')->__('Group'),
            'align' => 'left',
            'name' => 'group_name',
            'index' => 'entity_name'
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'editable' => true,
            'width' => 0,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/groupsgrid', array('id' => $this->getRight()->getId(), '_current' => true));
    }

    public function setSelectedGroups($selected) {
        foreach ($selected as $id) {
            $this->_selected[$id] = array('id' => $id);
        }
    }

    public function getSelected() {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();
            foreach ($this->getRight()->getLinkedGroups() as $right) {
                $this->_selected[$right->getGroupId()] = array('id' => $right->getGroupId());
            }
        }
        return $this->_selected;
    }

    public function _getSelected() {
        return array_keys($this->getSelected());
    }

    /**
     * Sets the currently selected items
     * 
     * @param array $selected
     */
    public function setSelected($selected) {
        if(!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'group_in_right') {
            $groupIds = $this->_getSelected();
            if (empty($groupIds)) {
                $groupIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $groupIds));
            } elseif (!empty($groupIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $groupIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    public function getRowUrl($row) {
        return null;
    }
    
}

