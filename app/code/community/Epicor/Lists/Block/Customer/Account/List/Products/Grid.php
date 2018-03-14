<?php

/**
 * List's  product Grid config
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    private $_selected = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('list_products');
        $this->setIdColumn('id');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array(
            'selected_products' => 1
        ));
    }
    
    protected function _prepareCollection()
    {
        $helper = Mage::helper('epicor_lists/frontend_product');
        /* @var $helper Epicor_Lists_Helper_Frontend_Product */
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
        
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('uom');
        $collection->addAttributeToSelect('sku');
        //prevent the observer event 
        $collection->setFlag('no_location_filtering', true);
        $collection->setFlag('no_product_filtering', true);
        
        if ($helper->hasFilterableLists() || $contractHelper->mustFilterByContract()) {
            $productIds = $helper->getActiveListsProductIds();
            $skus = $this->_getSelected();
            $collection->getSelect()->where(
                '(e.entity_id IN(' . $productIds . ') OR e.sku IN ("' . join('","', $skus) . '"))'
            );
        }
        $collection->addFieldToFilter('type_id', array(
            'neq' => 'grouped'
        ));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    
    protected function _mandatoryProducts($collection)
    {
        $listSettings = $this->getList()->getSettings();
        $restrict = false;
        if (in_array('M', $listSettings)) {
            $restrict = true;
            $ids      = array(
                0
            );
        }
        if ($restrict) {
            $ids = $this->_getSelected();
            if (!empty($ids)) {
                $collection->addFieldToFilter('sku', array(
                    'in' => $ids
                ));
            } else {
                $collection->addFieldToFilter('sku', array(
                    'in' => $ids
                ));
            }
            return $collection;
        }        
    }    
    
    protected function _toHtml()
    {
        $html = parent::_toHtml(false);
        return $html;
    }
    
    public function getRowUrl($row)
    {
        return '#';
    }
    
    protected function _prepareColumns()
    {
        $helper    = Mage::helper('epicor_lists');
        $typeModel = Mage::getModel('epicor_lists/list_type');
        
        $this->addColumn('selected_products', array(
            'index' => 'sku',
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_products',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'filter_index' => 'main_table.sku',
            'sortable' => false,
            'field_name' => 'links[]',
            'use_index' => true
        ));
        
        $this->addColumn('sku', array(
            'header' => $helper->__('Sku'),
            'index' => 'sku',
            'type' => 'text',
            'renderer' => new Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Skunodelimiter()
        ));
        
        
        $this->addColumn('uom', array(
            'header' => $helper->__('UOM'),
            'index' => 'uom',
            'type' => 'text'
        ));
        
        $this->addColumn('name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
            'type' => 'text'
        ));
        
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
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('list_id'));
            }
        }
        return $this->list;
    }
    
    /**
     * Builds the array of selected Products
     * 
     * @return array
     */
    public function getSelected()
    {   
        if(!$this->getList()->getId()){
            $selectedProducts = Mage::getSingleton('core/session')->getSelectedProducts(true);
            foreach ($selectedProducts as $product) {
                $this->_selected[$product] = array(
                    'sku' => $product
                );
            }
        }
        
        if (empty($this->_selected) && $this->getList()->getId()) {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */
            foreach ($list->getProducts() as $product) {
                $this->_selected[$product->getSku()] = array(
                    'sku' => $product->getSku()
                );
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
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'selected_products') {
            $skus = $this->_getSelected();
            if (empty($skus)) {
                $skus = array(
                    "0"
                );
            }
            if (($column->getFilter()->getValue()) && (!empty($skus))) {
                $this->getCollection()->addFieldToFilter('sku', array(
                    'in' => $skus
                ));
            } else {
                if ($skus) {
                    $this->getCollection()->addFieldToFilter('sku', array(
                        'nin' => $skus
                    ));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        
        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', array(
            '_current' => true
        ));
    }
    
}