<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 22/06/2016
 * Time: 14:48
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultFilter(array('in_products' => 1));
        $this->setSaveParametersInSession(false);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if(empty($productIds)) {
                $productIds = 0;
            }
            if($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'product',
            'values'            => $this->_getSelectedProducts(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('gallery')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('gallery')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('type', array(
            'header'    => Mage::helper('gallery')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('gallery')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        return parent::_prepareColumns();
    }

    protected function _getSelectedProducts() {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }

    public function getSelectedProducts() {
        $gallery = Mage::registry('current_gallery');
        if($gallery) {
            $selectedProducts = explode(',', $gallery->getProductIds());
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('entity_id', array('in', $selectedProducts));
        } else {
            $products = Mage::getModel('catalog/product')->getCollection();
        }
        $prodIds = array();
        foreach($products as $product) {
            $prodIds[$product->getId()] = array('position' => $product->getPosition());
        }
        return $prodIds;
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/productgrid',  array('_current' => true));
    }
}