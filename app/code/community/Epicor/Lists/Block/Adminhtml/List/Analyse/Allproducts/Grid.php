<?php

/**
 * 
 * ERP Account grid for erp account selector input
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Analyse_Allproducts_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('products_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $data = Mage::getSingleton('admin/session')->getAnalyseProductsData();
        $listId = $data['list_id'];
        $list = Mage::getModel('epicor_lists/list')->load($listId);
        /* @var $list Epicor_Lists_Model_List */
        $allSku = array_keys($list->getProducts());

        $productCollection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $productCollection->addAttributeToFilter('sku', array('in' => $allSku));
        $this->setCollection($productCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('sku', array(
            'header' => Mage::helper('epicor_comm')->__('Product SKU'),
            'index' => 'sku',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $row->getId();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('grid' => true, 'list_id' => $this->getRequest()->getParam('list_id')));
    }

}
