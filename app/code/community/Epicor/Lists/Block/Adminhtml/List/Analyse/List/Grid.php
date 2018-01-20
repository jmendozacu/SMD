<?php

/**
 * List admin actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Analyse_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('list_grid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('DESC');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setSaveParametersInSession(true);
        $this->setAdditionalJavaScript('var analyseListProductsGridUrl = "' . $this->getUrl('*/*/listproducts') . '";'.'var analyseListAllProductsGridUrl = "' . $this->getUrl('*/*/listallproducts') . '";');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_lists/list')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Collection */
        $collection->addFieldToFilter('main_table.id', array('in' => Mage::registry('epicor_lists_analyse_ids')));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'index' => 'id',
            'type' => 'number',
            'filter' => false,
            'sortable'  => false,
        ));
        
        $this->addColumn('priority', array(
            'header' => $helper->__('Priority'),
            'index' => 'priority',
            'type' => 'number',
            'filter' => false,
            'sortable'  => false,
        ));

        $typeModel = Mage::getModel('epicor_lists/list_type');
        /* @var $typeModel Epicor_Lists_Model_List_Type */

        $this->addColumn('type', array(
            'header' => $helper->__('Type'),
            'index' => 'type',
            'type' => 'options',
            'options' => $typeModel->toFilterArray(),
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('title', array(
            'header' => $helper->__('Title'),
            'index' => 'title',
            'type' => 'text',
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('erp_code', array(
            'header' => $helper->__('ERP Code'),
            'index' => 'erp_code',
            'type' => 'text',
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('start_date', array(
            'header' => $helper->__('Start Date'),
            'index' => 'start_date',
            'type' => 'datetime',
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('end_date', array(
            'header' => $helper->__('End Date'),
            'index' => 'end_date',
            'type' => 'datetime',
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('active', array(
            'header' => $helper->__('Active'),
            'index' => 'active',
            'type' => 'options',
            'options' => array(
                0 => $helper->__('No'),
                1 => $helper->__('Yes')
            ),
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Current Status'),
            'index' => 'active',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_active',
            'type' => 'options',
            'filter' => false,
            'sortable'  => false,
        ));

        $this->addColumn('source', array(
            'header' => $helper->__('Source'),
            'index' => 'source',
            'type' => 'text',
            'filter' => false,
            'sortable'  => false,
        ));
        
        $sku = $this->getRequest()->getPost('sku');
        $this->addColumn('product', array(
            'header' => $sku ? $helper->__('Product') : $helper->__('Total Products'),
            'filter' => false,
            'sku' => $sku,
            'sortable'  => false,
            'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_analyse_products'
        ));


        $this->addColumn('action', array(
            'header' => $helper->__('Action'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $helper->__('View'),
                    'onclick' => 'javascript: listsAnalyse.openpopup(this);',
                    'url' => 'javascript: void(0);'
                ),
                array(
                    'caption' => $helper->__('View All'),
                    'onclick' => 'javascript: listsAnalyse.openpopupallproducts(this);',
                    'url' => 'javascript: void(0);'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
            'filter' => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return '';
    }
    
}
