<?php

class Epicor_Common_Block_Adminhtml_Access_Group_Edit_Tab_Rights extends Mage_Adminhtml_Block_Widget_Grid {

    private $_selected = array();

    public function _construct() {
        $this->setId('accessRightGroupsGrid');
        $this->setDefaultSort('entity_name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->getGroup()->getId()) {
            $this->setDefaultFilter(array('right_in_group' => 1));
        }
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'right_in_group') {
            $rightIds = $this->_getSelected();
            if (empty($rightIds)) {
                $rightIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $rightIds));
            } elseif (!empty($rightIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $rightIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Gets the current group data
     * 
     * @return Epicor_Common_Model_Access_Group
     */
    public function getGroup() {

        if (!$this->_accessright) {
            $this->_accessright = Mage::registry('access_group_data');
        }
        return $this->_accessright;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_common/access_right')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getSelected() {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();

            foreach ($this->getGroup()->getLinkedRights() as $right) {
                $this->_selected[$right->getRightId()] = array('id' => $right->getRightId());
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
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    protected function _prepareColumns() {

        $this->addColumn('right_in_group', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'right[]',
            'name' => 'right',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'editable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('right_name', array(
            'header' => Mage::helper('epicor_common')->__('Access Right'),
            'align' => 'left',
            'name' => 'right_name',
            'index' => 'entity_name'
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

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/rightsgrid', array('id' => $this->getGroup()->getId(), '_current' => true));
    }

    public function getRowUrl($row) {
        return null;
    }

}

