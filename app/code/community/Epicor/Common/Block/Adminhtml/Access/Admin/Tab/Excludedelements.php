<?php

class Epicor_Common_Block_Adminhtml_Access_Admin_Tab_Excludedelements extends Mage_Adminhtml_Block_Widget_Grid {

    private $_selected = array();

    public function _construct() {
        $this->setId('excludedElementsGrid');
        $this->setDefaultSort('module');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('element_excluded' => 1));
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_common/access_element')->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('element_excluded', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'element_excluded',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'id',
            'editable' => true,
            'sortable' => false,
            'field_name' => 'links[]',
        ));

        $this->addColumn('module_name', array(
            'header' => Mage::helper('epicor_common')->__('Module'),
            'align' => 'left',
            'index' => 'module'
        ));

        $this->addColumn('controller_name', array(
            'header' => Mage::helper('epicor_common')->__('Controller'),
            'align' => 'left',
            'index' => 'controller'
        ));

        $this->addColumn('action_name', array(
            'header' => Mage::helper('epicor_common')->__('Action'),
            'align' => 'left',
            'index' => 'action'
        ));

        $this->addColumn('block_name', array(
            'header' => Mage::helper('epicor_common')->__('Block'),
            'align' => 'left',
            'index' => 'block'
        ));

        $this->addColumn('action_type', array(
            'header' => Mage::helper('epicor_common')->__('Action Type'),
            'align' => 'left',
            'index' => 'action_type'
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));


        return parent::_prepareColumns();
    }

    public function getRowClass(Varien_Object $row) {
        return $row->getId() ? '' : 'new';
    }

    public function getGridUrl() {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/excludedelementsgrid', array('_current' => true));
    }

    public function getSelected() {

        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();
            $collection = Mage::getModel('epicor_common/access_element')->getCollection();
            /* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
            $collection->addFieldToFilter('excluded', 1);
            foreach ($collection->getItems() as $element) {
                $this->_selected[$element->getId()] = array('id' => $element->getId());
            }
        }
        return $this->_selected;
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

    public function _getSelected() {
        return array_keys($this->getSelected());
    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'element_excluded') {
            $groupIds = $this->_getSelected();
            if (empty($groupIds)) {
                $groupIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in' => $groupIds));
            } elseif (!empty($groupIds)) {
                $this->getCollection()->addFieldToFilter('id', array('nin' => $groupIds));
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
