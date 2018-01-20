<?php

class Epicor_Common_Block_Adminhtml_Access_Right_Edit_Tab_Elements extends Mage_Adminhtml_Block_Widget_Grid {

    private $_selected = array();
    
    public function _construct() {
        $this->setId('accessRightElementsGrid');
        $this->setDefaultSort('module');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->getRight()->getId()) {
            $this->setDefaultFilter(array('element_in_right' => 1));
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
        $collection = Mage::getModel('epicor_common/access_element')->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Element_Collection */
        $collection->addFieldToFilter('excluded', 0);
        if(Mage::getStoreConfigFlag('epicor_b2b/registration/reg_portaltype')) {
            $collection->addFieldToFilter('portal_excluded', 0);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('element_in_right', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'element[]',
            'name' => 'element',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'editable' => true,
            'index' => 'id'
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
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/elementsgrid', array('id' => $this->getRight()->getId(), '_current' => true));
    }

    public function getSelected() {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $this->_selected = array();
            foreach ($this->getRight()->getLinkedElements() as $element) {
                $this->_selected[$element->getElementId()] = array('id' => $element->getElementId());
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
        if ($column->getId() == 'element_in_right') {
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
