<?php

/**
 * List Stores Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Stores extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('storesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_stores' => 1));
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_stores') {
            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array(0);
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.group_id', array('in' => $ids));
            } else {
                $this->getCollection()->addFieldToFilter('main_table.group_id', array('nin' => $ids));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Is this tab shown?
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab Label
     *
     * @return boolean
     */
    public function getTabLabel()
    {
        return 'Stores';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Stores';
    }

    /**
     * Is this tab hidden?
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
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
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->list;
    }

    /**
     * Build data for List Stores
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Stores
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('core/store_group')->getCollection();
        /* @var $collection Mage_Core_Model_Resource_Store_Group_Collection */
        $collection->getSelect()->join(
                array('website' => $collection->getTable('core/website')), 'main_table.website_id = website.website_id', array('website_name' => 'website.name')
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Stores
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Stores
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn('selected_stores', array(
            'header' => $helper->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_stores',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'group_id',
            'filter_index' => 'main_table.group_id',
            'sortable' => false,
            'field_name' => 'links[]'
        ));


        $this->addColumn(
                'website_title', array(
            'header' => $helper->__('Website Name'),
            'index' => 'website_name',
            'filter_index' => 'website.name',
            'type' => 'text'
                )
        );

        $this->addColumn(
                'group_title', array(
            'header' => $helper->__('Store Name'),
            'index' => 'name',
            'filter_index' => 'main_table.name',
            'type' => 'text'
                )
        );


        $this->addColumn('row_id', array(
            'header' => $helper->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'group_id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Used in grid to return selected Stores values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected Stores
     * 
     * @return array
     */
    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getStoreGroups() as $storeGroup) {
                $this->_selected[$storeGroup->getId()] = array('id' => $storeGroup->getId());
            }
        }
        return $this->_selected;
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
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getList()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorlists_list/storesgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Mage_Core_Model_Store_Group $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    public function getEmptyText()
    {
        $type = Mage::getModel('epicor_lists/list_type')->getListLabel($this->getList()->getType());
        return $this->__('No Stores Selected.%s not restricted by Store', $type);
    }

}
