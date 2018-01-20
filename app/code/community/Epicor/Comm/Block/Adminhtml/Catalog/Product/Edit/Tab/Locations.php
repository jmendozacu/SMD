<?php

/**
 * Product Message Log Grid
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Locations extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_locations');
        $this->setDefaultSort('locations_code');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
        $this->setRowInitCallback('productLocations.rowInit.bind(productLocations)');
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Add'),
                            'onclick' => 'productLocations.add()',
                            'class' => 'task'
                        ))
        );
        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _getProduct()
    {
        if (!Mage::registry('current_product')) {
            Mage::register('current_product', Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id')));
        }
        return Mage::registry('current_product');
    }

    protected function _prepareCollection()
    {
        $id = $this->_getProduct()->getId();
        $collection = Mage::getModel('epicor_comm/location_product')->getCollection();


        /* @var $collection Epicor_Comm_Model_Mysql4_Location_Product_Collection */
        $collection->addFieldToFilter('main_table.product_id', $id);
        $collection->joinLocationInfo();
        $collection->joinExtraProductInfo($this->_getStore()->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('location_code', array(
            'header' => Mage::helper('epicor_comm')->__('Location Code'),
            'align' => 'center',
            'index' => 'location_code',
            'filter_index' => 'main_table.location_code',
            'column_css_class' => 'col-location_code',
        ));

        $this->addColumn('stock_status', array(
            'header' => Mage::helper('epicor_comm')->__('Stock Status'),
            'align' => 'left',
            'index' => 'stock_status',
            'filter_index' => 'main_table.stock_status',
            'column_css_class' => 'col-stock_status',
        ));

        $this->addColumn('free_stock', array(
            'header' => Mage::helper('epicor_comm')->__('Free Stock'),
            'align' => 'left',
            'index' => 'free_stock',
            'filter_index' => 'main_table.free_stock',
            'type' => 'number',
            'column_css_class' => 'col-free_stock',
        ));

        $this->addColumn('minimum_order_qty', array(
            'header' => Mage::helper('epicor_comm')->__('Min. Order Qty'),
            'align' => 'left',
            'index' => 'minimum_order_qty',
            'filter_index' => 'main_table.minimum_order_qty',
            'type' => 'number',
            'column_css_class' => 'col-minimum_order_qty',
        ));

        $this->addColumn('maximum_order_qty', array(
            'header' => Mage::helper('epicor_comm')->__('Max. Order Qty'),
            'align' => 'left',
            'index' => 'maximum_order_qty',
            'filter_index' => 'main_table.maximum_order_qty',
            'type' => 'number',
            'column_css_class' => 'col-maximum_order_qty',
        ));

        $this->addColumn('base_price', array(
            'header' => Mage::helper('epicor_comm')->__('Price'),
            'align' => 'left',
            'index' => 'base_price',
            'type' => 'currency',
            'currency' => 'currency_code',
            'filter_condition_callback' => array($this, '_filterPriceCondition'),
            'column_css_class' => 'col-base_price',
        ));

        $this->addColumn('lead_time', array(
            'header' => Mage::helper('epicor_comm')->__('Lead Time'),
            'align' => 'left',
            'index' => 'lead_time_days',
            'filter_index' => 'main_table.lead_time_days',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Product_Locations_Leadtime(),
        ));

        $this->addColumn('supplier_brand', array(
            'header' => Mage::helper('epicor_comm')->__('Supplier Brand'),
            'align' => 'left',
            'index' => 'supplier_brand',
            'filter_index' => 'main_table.supplier_brand',
            'column_css_class' => 'col-supplier_brand',
        ));

        $this->addColumn('tax_code', array(
            'header' => Mage::helper('epicor_comm')->__('Tax Code'),
            'align' => 'left',
            'index' => 'tax_code',
            'filter_index' => 'main_table.tax_code',
            'column_css_class' => 'col-tax_code',
        ));


        $this->addColumn('manufacturers', array(
            'header' => Mage::helper('epicor_comm')->__('Manufacturers'),
            'align' => 'left',
            'index' => 'manufacturers',
            'filter_index' => 'main_table.manufacturers',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Product_Locations_Manufacturers(),
            'column_css_class' => 'col-manufacturers',
        ));
        $this->addColumn('sort_order', array(
            'header' => Mage::helper('epicor_comm')->__('Sort Order'),
            'align' => 'left',
            'index' => 'sort_order',
            'filter_index' => 'main_table.sort_order',
            'column_css_class' => 'col-sort_order',
        ));

        $this->addColumn('actions', array(
            'header' => Mage::helper('epicor_comm')->__('Actions'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Edit'),
                    'url' => array('base' => 'adminhtml/epicorcomm_catalog_product/editlocation'),
                    'field' => 'id',    
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => 'adminhtml/epicorcomm_catalog_product/deletelocation'),
                    'field' => 'id',    
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
        ));
        
        $this->addColumn('rowdata', array(
            'header' => Mage::helper('flexitheme')->__('Row'),
            'align' => 'left',
            'name' => 'rowdata',
            'width' => 0,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',
        ));
        return parent::_prepareColumns();
    }

    /**
     * 
     * @param Epicor_Comm_Model_Mysql4_Location_Product_Collection $collection
     * @param type $column
     * @return type
     */
    public function _filterPriceCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        if (array_key_exists('from', $value)) {
            $this->getCollection()->getSelect()->where(// main sql call
                    "COALESCE(`store_location_product_info`.`base_price`,
            `store_location_product_info`.`base_price`) >= ?", $value['from']);
        }
        if (array_key_exists('to', $value)) {
            $this->getCollection()->getSelect()->where(// main sql call
                    "COALESCE(`store_location_product_info`.`base_price`,
            `store_location_product_info`.`base_price`) <= ?", $value['to']);
        }
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->_getProduct()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_catalog_product/locationsgrid', $params);
    }

//    public function getTabUrl()
//    {
//        return $this->getUrl('adminhtml/epicorcomm_catalog_product/locations', array('_current' => true));
//    }

    public function getSkipGenerateContent()
    {
        return false;
    }

    public function getRowUrl($row)
    {
        return "javascript:productLocations.rowEdit(this, " . $row->getId() . ");";
    }

    public function getTabClass()
    {
        return 'ajax notloaded';
    }

}
