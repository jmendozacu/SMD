<?php

class Epicor_Customerconnect_Block_Customer_Skus_List_Grid extends Epicor_Common_Block_Generic_List_Search { //Mage_Adminhtml_Block_Widget_Grid { //

    public function __construct() {
        parent::__construct();

        $this->setId('customerconnect_skus');
        $this->setDefaultSort('product_sku');
        $this->setDefaultDir('desc');
        $this->setIdColumn('entity_id');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportToCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportToXml'));
    }

    protected function initColumns() {

        $columns = array(
//            'entity_id' => array(
//                'index' => 'entity_id',
//                'header' => 'ID',
//                'type' => 'range',
//                'sortable' => true,
//                'filter_index' => 'main_table.entity_id'
//            ),
            'product_sku' => array(
                'index' => 'product.sku',
                'header' => 'Product SKU',
                'type' => 'text',
                'sortable' => true,
            ),
            'customer_sku' => array(
                'index' => 'sku',
                'header' => 'My SKU',
                'type' => 'text',
                'sortable' => true,
                'filter_index' => 'main_table.sku'
            ),
            'description' => array(
                'index' => 'description',
                'header' => 'Description',
                'type' => 'text',
                'sortable' => true,
            )
        );

        if (Mage::helper('customerconnect/skus')->canCustomerEditCpns()) {
            
            $columns['edit'] = array(
                'header' => $this->__('Edit'),
                'width' => '80',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'url' => array('base' => '*/*/edit',
                            'params' => array('id' => $this->getRequest()->getParam('id'))
                        ),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            );
            
            $columns['delete'] = array(
                'header' => $this->__('Delete'),
                'width' => '80',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Delete'),
                        'url' => array('base' => '*/*/delete',
                            'params' => array('id' => $this->getRequest()->getParam('id'))
                        ),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            );
        }

        $this->setCustomColumns($columns);
    }

    protected function _prepareCollection() {

        $this->setCollection($this->helper('customerconnect/skus')->getCustomerSkus());

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
}
