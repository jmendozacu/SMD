<?php

/**
 * F.A.Q. adminhtml edition grid
 * 
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 *
 */
class Epicor_Faqs_Block_Adminhtml_Faqs_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Init Grid default properties
     *
     */
    public function __construct() {
        //Configuring grid, sorting by weight
        parent::__construct();
        $this->setId('faqs_list_grid');
        if (Mage::helper('epicor_faqs')->getSortParameter() == 'usefulness') {
            $this->setDefaultSort('usefulness');
            $this->setDefaultDir('DSC');
        } else {
            $this->setDefaultSort('weight');
            $this->setDefaultDir('ASC');
        }
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Epicor_Faqs_Block_Adminhtml_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_faqs/faqs')->getResourceCollection()
                ->addExpressionFieldToSelect('usefulness', 
                        '(useful-useless)', array('useful'=>'useful', 'useless'=>'useless'));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns() {
        //Id column 
        $this->addColumn('faqs_id', array(
            'header' => Mage::helper('epicor_faqs')->__('ID'),
            'width' => '50px',
            'index' => 'faqs_id',
        ));
        //Weight column for sorting
        $this->addColumn('weight', array(
            'header' => Mage::helper('epicor_faqs')->__('Weight'),
            'index' => 'weight',
        ));
        //Usefulness=upvotes-downvotes
        $this->addColumn('usefulness', array(
            'header' => Mage::helper('epicor_faqs')->__('Usefulness'),
            'index' => 'usefulness',
        ));
        //Question column
        $this->addColumn('question', array(
            'header' => Mage::helper('epicor_faqs')->__('Question'),
            'width' => '320px',
            'index' => 'question',
        ));
        //Answer column
        $this->addColumn('answer', array(
            'header' => Mage::helper('epicor_faqs')->__('Answer'),
            'index' => 'answer',
        ));
        //Keywords column
        $this->addColumn('keywords', array(
            'header' => Mage::helper('epicor_faqs')->__('Keywords'),
            'index' => 'keywords',
        ));
        //Stores list
        $this->addColumn('stores', array(
            'header' => Mage::helper('epicor_faqs')->__('Stores'),
            'index' => 'stores',
            'width' => '170px',
            'renderer' => new Epicor_Faqs_Block_Adminhtml_Faqs_Column_Renderer_Stores(),
            'filter_condition_callback' => array($this, 'filterStores')
        ));
        //Creation timestamp
        $this->addColumn('created_at', array(
            'header' => Mage::helper('epicor_faqs')->__('Created'),
            'sortable' => true,
            'width' => '170px',
            'index' => 'created_at',
            'type' => 'datetime',
        ));
        //Edition link column
        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_faqs')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(array(
                    'caption' => Mage::helper('epicor_faqs')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'faqs',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Custom filter to store column
     *
     * @param $collection Epicor_Faqs_Model_Resource_Faqs_Collection
     * @param $column
     * @return void
     */
    protected function filterStores($collection, $column){
        /* @var $store_collection Mage_Core_Model_Resource_Store_Collection */
        $store_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('name', array('like' => '%' . $column->getFilter()->getValue() . '%'));

        $store_filter = array();
        foreach($store_collection as $store){
            $store_filter[] = array('finset' => $store->getId());
        }
        $store_filter = empty($store_filter) ? array('eq' => false) : $store_filter;

        $collection->addFieldToFilter('stores', $store_filter);

    }

}