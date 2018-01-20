<?php

/**
 * List Brands Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Brands extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('brandsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setRowInitCallback("initListBrand('brands_form','brandsGrid');");
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
        return 'Brands';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Brands';
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
     * Build data for List Brands
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Brands
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_lists/list_brand')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Brand_Collection */
        $collection->addFieldToFilter('list_id', $this->getList()->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Brands
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Brands
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn('company', array(
            'header' => $helper->__('Company'),
            'index' => 'company',
            'type' => 'text'
        ));

        $this->addColumn('site', array(
            'header' => $helper->__('Site'),
            'index' => 'site',
            'type' => 'text'
        ));

        $this->addColumn('warehouse', array(
            'header' => $helper->__('Warehouse'),
            'index' => 'warehouse',
            'type' => 'text'
        ));

        $this->addColumn('group', array(
            'header' => $helper->__('Group'),
            'index' => 'group',
            'filter_index' => 'main_table.group',
            'type' => 'text'
        ));

        $this->addColumn('actions', array(
            'header' => $helper->__('Actions'),
            'width' => '100',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $helper->__('Edit'),
                    'onclick' => 'javascript: listBrand.rowEdit(this)',
                ),
                array(
                    'caption' => $helper->__('Delete'),
                    'onclick' => 'javascript: if(window.confirm(\''
                    . addslashes($this->escapeHtml($helper->__('Are you sure you want to do this?')))
                    . '\')){listBrand.rowDelete(this);} return false;',
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
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

        return parent::_prepareColumns();
    }

    /**
     * Used in grid to return selected Brands values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected Brands
     * 
     * @return array
     */
    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getBrands() as $brand) {
                $this->_selected[$brand->getId()] = array('id' => $brand->getId());
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
        return $this->getUrl('adminhtml/epicorlists_list/brandsgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Mage_Core_Model_Brand_Group $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Add'),
                    'onclick' => "listBrand.add();",
                    'class' => 'task'
                ))
        );
        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }
    
    
    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
//    protected function _setCollectionOrder($column)
//    {
//        $collection = $this->getCollection();
//        if ($collection && $column->getIndex() == 'group') {
//            $collection->setOrder('`group`', strtoupper($column->getDir()));
//        } else {
//            parent::_setCollectionOrder($column);
//        }
//        
//        return $this;
//    }

}
