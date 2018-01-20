<?php

/**
 * List Products Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
//   implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function _construct()
    {
        parent::_construct();

        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $currencyHelper = Mage::helper('epicor_common/locale_format_currency');
        /* @var $currencyHelper Epicor_Common_Helper_Locale_Format_Currency */

        $this->setId('productsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_products' => 1));
        $this->setAdditionalJavaScript("initListProduct({
                table: 'productsGrid',
                listId: '" . $this->getList()->getId() . "',
                jsonPricing: 'json_pricing',
                translations: {
                    'Currency': '" . htmlentities($helper->__('Currency')) . "',
                    'Price': '" . htmlentities($helper->__('Price')) . "',
                    'Breaks': '" . htmlentities($helper->__('Breaks')) . "',
                    'Value Breaks': '" . htmlentities($helper->__('Value Breaks')) . "',
                    'Qty': '" . htmlentities($helper->__('Qty')) . "',
                    'Value': '" . htmlentities($helper->__('Value')) . "',
                    'Description': '" . htmlentities($helper->__('Description')) . "',
                    'Select': '" . htmlentities($helper->__('Select')) . "',
                    'Add': '" . htmlentities($helper->__('Add')) . "',
                    'Delete': '" . htmlentities($helper->__('Delete')) . "',
                    'Clone': '" . htmlentities($helper->__('Clone')) . "',
                    'No records found.': '" . htmlentities($helper->__('No records found.')) . "',
                    'Please choose a file.': '" . htmlentities($helper->__('Please choose a file.')) . "',
                },
                url: '" . $this->getUrl('adminhtml/epicorlists_list/productpricing') . "',
                importUrl: '" . $this->getUrl('adminhtml/epicorlists_list/productsimportpost', array('id' => $this->getList()->getId())) . "',
                csvDowloadUrl: '" . $this->getUrl('adminhtml/epicorlists_list/productimportcsv') . "',
                currencies: " . json_encode($currencyHelper->getAllowedCurrencies()) . ",
                pricingIsEditable: " . ($this->getList()->getTypeInstance()->isSectionEditable('pricing') ? 'true' : 'false') . ",
            });");
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_products') {
            $ids = $this->_getSelected();
            if (!empty($ids)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('sku', array('in' => $ids));
                } else if ($ids) {
                    $this->getCollection()->addFieldToFilter('sku', array('nin' => $ids));
                }
            } else {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('sku', array('in' => ''));
                } else {
                    $this->getCollection()->addFieldToFilter('sku', array('nin' => ''));
                }
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
        return 'Products';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Products';
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
     * @return Epicor_Lists_Model_List
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
     * Build data for List Products
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Epicor_Comm_Model_Product */
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('uom');
        $collection->addAttributeToSelect('configurator');
        $collection->getSelect()->joinLeft(
                array('lp' => $collection->getTable('epicor_lists/list_product')), 'e.sku = lp.sku AND lp.list_id = "' . $this->getList()->getId() . '"', array('qty')
        );
        //If the type is contract then search for the product
        if ($this->getList()->getType() == "Co") {
            $collection->getSelect()->joinLeft(
                    array('cp' => $collection->getTable('epicor_lists/contract_product')), 'cp.list_product_id =lp.id', array('start_date', 'line_number', 'part_number', 'end_date', 'status', 'is_discountable', 'min_order_qty', 'max_order_qty')
            );
        }

        if ($this->getList()->getTypeInstance()->isSectionEditable('products') == false) {
            $ids = $this->_getSelected();
            if (!empty($ids)) {
                $collection->addFieldToFilter('sku', array('in' => $ids));
            }
        }

        //Filter Groupped Products From the List
        $collection->addFieldToFilter('type_id', array('neq' => 'grouped'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Products
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        if ($this->getList()->getTypeInstance()->isSectionEditable('products')) {
            $this->addColumn('selected_products', array(
                'header' => $helper->__('Select'),
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'selected_products',
                'values' => $this->_getSelected(),
                'align' => 'center',
                'index' => 'sku',
                'filter_index' => 'main_table.sku',
                'sortable' => false,
                'field_name' => 'links[]',
                'use_index' => true
            ));
        }

        $this->addColumn(
                'sku', array(
            'header' => $helper->__('SKU'),
            'index' => 'sku',
            'filter_index' => 'sku',
            'type' => 'text',
            'sortable' => true,
            'renderer' => new Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Skunodelimiter(),
                )
        );
        $this->addColumn(
                'uom', array(
            'header' => $helper->__('UOM'),
            'index' => 'uom',
            'filter_index' => 'uom', // put in as sorting wouldn't work properly, might need to look again 
            'type' => 'text',
            'sortable' => true,
            'filterable' => true,
                )
        );

        $this->addColumn(
                'type_id', array(
            'header' => $helper->__('Product Type'),
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            'filter_index' => 'type_id',
            'sortable' => true,
            'filterable' => true,
                )
        );

        $this->addColumn(
                'product_name', array(
            'header' => $helper->__('Product Name'),
            'index' => 'name',
            'filter_index' => 'name',
            'type' => 'text'
                )
        );

        if ($this->getList()->getType() == "Co") {

            $this->addColumn(
                    'line_number', array(
                'header' => $helper->__('Line Number'),
                'index' => 'line_number',
                'filter_index' => 'line_number',
                'type' => 'text',
                'sortable' => false,
                'filterable' => true,
                'filter_condition_callback' => array($this, '_LineFilter'),
                    )
            );
            $this->addColumn(
                    'part_number', array(
                'header' => $helper->__('Part Number'),
                'index' => 'part_number',
                'filter_index' => 'part_number',
                'type' => 'text',
                'sortable' => false,
                'filterable' => true,
                'filter_condition_callback' => array($this, '_PartFilter'),
                    )
            );
            $this->addColumn(
                    'start_date', array(
                'header' => $helper->__('Start Date'),
                'index' => 'start_date',
                'filter_index' => 'start_date',
                'type' => 'datetime',
                'sortable' => false,
                'filterable' => false,
                'filter' => false,
                    )
            );
            $this->addColumn(
                    'end_date', array(
                'header' => $helper->__('End Date'),
                'index' => 'end_date',
                'filter_index' => 'end_date',
                'type' => 'datetime',
                'sortable' => false,
                'filterable' => false,
                'filter' => false,
                    )
            );
            $this->addColumn('status', array(
                'header' => $helper->__('Status'),
                'width' => '50',
                'index' => 'status',
                'align' => 'center',
                'type' => 'options',
                'sortable' => false,
                'options' => array('1' => 'Enabled', '0' => 'Disabled'),
                'filter_condition_callback' => array($this, '_StatusFilter')
            ));
            $this->addColumn(
                    'is_discountable', array(
                'header' => $helper->__('Discountable'),
                'index' => 'is_discountable',
                'filter_index' => 'is_discountable',
                'type' => 'options',
                'sortable' => false,
                'filterable' => true,
                'options' => array(
                    0 => $helper->__('No'),
                    1 => $helper->__('Yes')
                ),
                'filter_condition_callback' => array($this, '_DiscountFilter')
                    )
            );
            $this->addColumn(
                    'max_order_qty', array(
                'header' => $helper->__('Quanities'),
                'index' => 'max_order_qty',
                'filter_index' => 'max_order_qty',
                'type' => 'options',
                'sortable' => false,
                'filterable' => false,
                'filter' => false,
                'renderer' => new Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Contractquantities()
                    )
            );
        }

        if ($this->getList()->getTypeInstance()->isSectionVisible('pricing')) {
            $this->addColumn('actions', array(
                'header' => $helper->__('Actions'),
                'width' => '100',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Pricing'),
                        'onclick' => 'return listProduct.pricing(this, event);',
                        'href' => 'javascript:void(0);',
                        'conditions' => array('configurator' => array('0', 0, 'empty', 'null')),
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
                'links' => 'true',
            ));
        }

        $this->addColumn('row_id', array(
            'header' => $helper->__('Position'),
            'name' => 'row_id',
            'type' => 'input',
            'validate_class' => 'validate-number',
            'index' => 'sku',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Used in grid to return selected Products values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_map('strval', array_keys($this->getSelected()));
    }

    /**
     * Builds the array of selected Products
     * 
     * @return array
     */
    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getProducts() as $product) {
                $this->_selected[$product->getSku()] = array('sku' => $product->getSku());
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
        return $this->getUrl('adminhtml/epicorlists_list/productsgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Epicor_Comm_Model_Product $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    /**
     * Row _uomFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    public function _uomFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        /* @var $delimiter epicor_lists_helper_messaging_customer */
        $delimiter = Mage::helper('epicor_lists/messaging_customer')->getUOMSeparator();

        echo $this->getCollection()->getSelect();

        // if unable to get a value of the column don't attempt filter 
        if (!$value) {
            return $this;
        }
        $this->getCollection()->getSelect()
                ->where("e.sku like ?", "%" . $delimiter . $value . "%");

        $collection->getSelect()->order('sku');
    }

    /**
     * Row _LineFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _LineFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();
        if (!$filterroleid) {
            return $this;
        }
        $this->getCollection()->getSelect()
                ->where("cp.line_number like ?", "%" . $filterroleid . "%");

        return;
    }

    /**
     * Row _PartFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _PartFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();
        if (!$filterroleid) {
            return $this;
        }
        $this->getCollection()->getSelect()
                ->where("cp.part_number like ?", "%" . $filterroleid . "%");

        return;
    }

    /**
     * Row _StatusFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _StatusFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();

        $this->getCollection()->getSelect()->where("cp.status =" . $filterroleid);

        return;
    }

    /**
     * Row _DiscountFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _DiscountFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();

        $this->getCollection()->getSelect()->where("cp.is_discountable =" . $filterroleid);

        return;
    }

    /**
     * Row _startDateFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _startDateFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();
        if ($filterroleid['orig_from']) {
            $dateStart = date('Y-m-d', strtotime($filterroleid['orig_from']));
        }
        if ($filterroleid['orig_to']) {
            $dateEnd = date('Y-m-d', strtotime($filterroleid['orig_to']));
        }
        if (($filterroleid['orig_from']) || ($filterroleid['orig_to'])) {
            $dateCondition = $this->dateCondition($dateStart, $dateEnd, $column->getId());
        }
        if ($dateCondition) {
            $this->getCollection()->getSelect()->where($dateCondition);
        }
        return;
    }

    /**
     * Row _EndDateFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _EndDateFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();

        if ($filterroleid['orig_from']) {
            $dateStart = date('Y-m-d', strtotime($filterroleid['orig_from']));
        }
        if ($filterroleid['orig_to']) {
            $dateEnd = date('Y-m-d', strtotime($filterroleid['orig_to']));
        }
        if (($filterroleid['orig_from']) || ($filterroleid['orig_to'])) {
            $dateCondition = $this->dateCondition($dateStart, $dateEnd, $column->getId());
        }
        if ($dateCondition) {
            $this->getCollection()->getSelect()->where($dateCondition);
        }

        return;
    }

    public function dateCondition($dateFrom = null, $dateEnd = null, $columnName)
    {

        if (!empty($dateFrom) && !empty($dateEnd)) {
            $dateConditions = " cp." . $columnName . " BETWEEN '{$dateFrom}' AND '{$dateEnd}'";
        } else if (!empty($dateFrom) && empty($dateEnd)) {
            $dateConditions = " cp." . $columnName . " >= '{$dateFrom}'";
        } else if (empty($dateFrom) && !empty($dateEnd)) {
            $dateConditions = " cp." . $columnName . " >= '{$dateEnd}'";
        } elseif (empty($dateFrom) && empty($dateTo)) {
            $dateConditions = '';
        }
        return $dateConditions;
    }

    public function getSelectButtonHtml()
    {
        return $this->getChildHtml('select_all');
    }

    public function getUnselectButtonHtml()
    {
        return $this->getChildHtml('unselect_all');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
                'select_all', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Select All'),
                            'onclick' => "javascript:productSelectAll()",
                            'class' => 'task'
                        ))
        );

        $this->setChild(
                'unselect_all', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Unselect All'),
                            'onclick' => "javascript:productUnselectAll()",
                            'class' => 'task'
                        ))
        );

        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $isEditable = $this->getList()->getTypeInstance()->isSectionEditable('products');
        $html = '';
        if ($isEditable) {
            $html .= $this->getSelectButtonHtml();
            $html .= $this->getUnselectButtonHtml();
        }
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

}
