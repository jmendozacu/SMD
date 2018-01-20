<?php

class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproducts_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_filterData;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('addProductSearchGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setRowClickCallback('addProductSearchGrid.productGridRowClick.bind(addProductSearchGrid)');
        $this->setCheckboxCheckCallback('addProductSearchGrid.productGridCheckboxCheck.bind(addProductSearchGrid)');
        $this->setRowInitCallback('addProductSearchGrid.productGridRowInit.bind(addProductSearchGrid)');
        $this->setUseAjax(true);
        
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $products Mage_Catalog_Model_Resource_Product_Collection */

        $collection //->addAttributeToFilter('google_feed', true)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_products', array(
            'header' => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id',
            'sortable' => false,
        ));
        
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('sales')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('sales')->__('Product Name'),
            'index' => 'name'
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('sales')->__('SKU'),
            'width' => '80',
            'index' => 'sku'
        ));
        $this->addColumn('price', array(
            'header' => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align' => 'center',
            'type' => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate' => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index' => 'price',
            'renderer'  => 'Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproducts_Renderer_customprice',
            //'renderer' => 'adminhtml/sales_order_create_search_grid_renderer_price'
        ));
        
        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty To Add'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));
        return parent::_prepareColumns();
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', array());

        return $products;
    }

    protected function getStore()
    {
        return Mage::getSingleton('adminhtml/session_quote')->getStore();
    }
    
     public function getGridUrl()
    {
        return $this->getUrl('*/*/productsearchgrid', array('_current'=>true));
    } 

}