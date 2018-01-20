<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tab_Details_Lines_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_defaultLimit = 10000;
    
    public function __construct() {
        parent::__construct();
        $this->setId('lines');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);

    }

    protected function _prepareCollection() {
        $return = Mage::registry('return');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $lines = Mage::getModel('epicor_comm/customer_return_line')->getCollection();
        /* @var $lines Epicor_Comm_Model_Mysql4_Customer_Return_Line_Collection */
        $lines->addFieldToFilter('return_id', array('eq' => $return->getId()));

        $this->setCollection($lines);

        return parent::_prepareCollection();
    }

    public function getRowUrl($row) {
        return false;
    }

    protected function _prepareColumns() {

        $columns = array(
            'entity_id' => array(
                'header' => Mage::helper('epicor_comm')->__('Line'),
                'align' => 'left',
                'index' => 'id',
                'type' => 'number',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Number(),
            ),
            'product_code' => array(
                'header' => Mage::helper('epicor_comm')->__('SKU'),
                'align' => 'left',
                'index' => 'product_code',
                'type' => 'text',
                'filterable' => false,
                'sortable' => false,
            ),
            'unit_of_measure_code' => array(
                'header' => Mage::helper('epicor_comm')->__('UOM'),
                'align' => 'left',
                'index' => 'unit_of_measure_code',
                'type' => 'text',
                'filterable' => false,
                'sortable' => false,
            ),
            'qty' => array(
                'header' => Mage::helper('epicor_comm')->__('Qty'),
                'align' => 'left',
                'index' => 'qty',
                'type' => 'text',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Qty(),
            ),
            'return_code' => array(
                'header' => Mage::helper('epicor_comm')->__('Return Code'),
                'align' => 'left',
                'index' => 'return_code',
                'type' => 'text',
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Returncode(),
            ),
            'note_text' => array(
                'header' => Mage::helper('epicor_comm')->__('Notes'),
                'align' => 'left',
                'index' => 'note_text',
                'type' => 'text',
                'filterable' => false,
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Notes(),
            ),
            'source' => array(
                'header' => Mage::helper('epicor_comm')->__('Source'),
                'align' => 'left',
                'index' => 'source',
                'type' => 'text',
                'filterable' => false,
                'sortable' => false,
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Source(),
            ),
            'attachments' => array(
                'header' => Mage::helper('epicor_comm')->__('Attachments'),
                'align' => 'left',
                'filterable' => false,
                'index' => 'attachments',
                'renderer' => new Epicor_Comm_Block_Customer_Returns_Lines_Renderer_Attachments(),
                'type' => 'text',
                'filter' => false,
                'keep_data_format' => 1,
            )
        );

        $helper = Mage::helper('epicor_comm/returns');
        /* @var $helper Epicor_Comm_Helper_Returns */

        $return = Mage::registry('return');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $this->addColumn('entity_id', $columns['entity_id']);
        $this->addColumn('product_code', $columns['product_code']);
        $this->addColumn('unit_of_measure_code', $columns['unit_of_measure_code']);
        $this->addColumn('qty', $columns['qty']);
        $this->addColumn('return_code', $columns['return_code']);
        $this->addColumn('note_text', $columns['note_text']);
        $this->addColumn('source', $columns['source']);

        if ($helper->checkConfigFlag('line_attachments', $return->getReturnType(), $return->getStoreId())) {
            $this->addColumn('attachments', $columns['attachments']);
        }

        parent::_prepareColumns();
    }

}
