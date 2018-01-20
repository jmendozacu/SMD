<?php

/**
 * List Addresses Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Addresses extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('restrictedaddressGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_addresses' => 1));
        $this->setRowInitCallback("initListRestrictionAddress('restrictions_form','restrictedaddressGrid');");
    }

    /**
     * Gets the List for this tab
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('list_id'));
            }
        }
        return $this->list;
    }

    /**
     * Build data for List Restricted Addresses
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Restrictions_Addresses
     */
    protected function _prepareCollection()
    {
        $restrictionType = Mage::getSingleton('admin/session')->getRestrictionTypeValue();

        $collection = Mage::getModel('epicor_lists/list_address')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Address_Collection */
        $collection->addFieldToFilter('main_table.list_id', $this->getList()->getId());

        $restrictionTable = $collection->getTable('epicor_lists/list_address_restriction');
        $collection->getSelect()->join(array('r' => $restrictionTable), 'r.address_id = main_table.id', array());
        $collection->addFieldToFilter('r.restriction_type', $restrictionType);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Build columns for List restricted Addresses
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Addresses
     */
    protected function _prepareColumns()
    {
        $restrictionType = Mage::getSingleton('admin/session')->getRestrictionTypeValue();
        $id = $this->getList()->getId();
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        if ($restrictionType == Epicor_Lists_Model_List_Address_Restriction::TYPE_ADDRESS) {
            $this->addColumn(
                'address_name', array(
                'header' => $helper->__('Name'),
                'index' => 'name',
                'type' => 'text'
                )
            );

            $this->addColumn(
                'flatt_address', array(
                'header' => $helper->__('Address'),
                'index' => 'address1',
                'type' => 'text',
                'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_list_address',
                'filter_condition_callback' => array($this, '_addressFilter'),
                )
            );
        }

        $this->addColumn(
            'country', array(
            'header' => $helper->__('Country'),
            'index' => 'country',
            'type' => 'country',
            'width' => '200px'
            )
        );

        if ($restrictionType == Epicor_Lists_Model_List_Address_Restriction::TYPE_STATE) {
            $this->addColumn(
                'county', array(
                'header' => $helper->__('County'),
                'index' => 'county',
                'type' => 'text'
                )
            );
        }
        if ($restrictionType == Epicor_Lists_Model_List_Address_Restriction::TYPE_ZIP) {
            $this->addColumn(
                'postcode', array(
                'header' => $helper->__('Postcode'),
                'index' => 'postcode',
                'type' => 'text',
                'renderer' => 'epicor_lists/adminhtml_list_edit_tab_restrictions_renderer_postcode',
                )
            );
        }

        if ($this->isSectionEditable()) {
            $this->addColumn('actions', array(
                'header' => $helper->__('Actions'),
                'width' => '100',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Edit'),
                    ),
                    array(
                        'caption' => $helper->__('Delete'),
                        'onclick' => 'javascript: if(window.confirm(\''
                        . addslashes($this->escapeHtml($helper->__('Are you sure you want to do this?')))
                        . '\')){listRestrictionAddress.rowDelete(this);} return false;',
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
                'renderer' => 'epicor_lists/adminhtml_list_edit_tab_restrictions_renderer_action',
                'links' => 'true',
            ));


            $this->addColumn('rowdata', array(
                'header' => Mage::helper('flexitheme')->__(''),
                'align' => 'left',
                'width' => '1',
                'name' => 'rowdata',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
                'column_css_class' => 'no-display last',
                'header_css_class' => 'no-display last',
            ));
        }
        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_addresses') {
            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.id', array('in' => $ids));
            } else if ($ids) {
                $this->getCollection()->addFieldToFilter('main_table.id', array('nin' => $ids));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Used in grid to return selected Customers values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected Customers
     * 
     * @return array
     */
    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getAddresses() as $address) {
                $this->_selected[$address->getId()] = array('id' => $address->getId());
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

    protected function _addressFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $clone = clone $collection;

        $filterIds = array();
        foreach ($clone->getItems() as $item) {
            /* @var $item Epicor_Lists_Model_List */
            if (stripos($item->getFlattenedAddress(), $value) !== false) {
                $filterIds[] = $item->getId();
            }
        }

        $collection->addFieldToFilter('id', array('in' => $filterIds));
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        $params = array(
            'list_id' => $this->getList()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorlists_list/restrictionsgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Epicor_Lists_Model_List_Address $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
        //  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    protected function _prepareLayout()
    {
        $restrictionType = Mage::getSingleton('admin/session')->getRestrictionTypeValue();
        $id = $this->getList()->getId();

        if ($this->isSectionEditable()) {
            $this->setChild(
                'add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('adminhtml')->__('Add'),
                        'onclick' => "openRestrictionForm('',$id,'add','" . $restrictionType . "');",
                        'class' => 'task'
                    ))
            );
        }
        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function isSectionEditable()
    {
        return 1;
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
        return 'Restrictions';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Restrictions';
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

}
