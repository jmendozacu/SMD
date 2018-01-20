<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('returnsgrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/customer_return')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Return_Collection */
        $table = $collection->getTable('epicor_comm/customer_erpaccount');

        $collection->getSelect()->joinLeft(array('cc' => $table), 'entity_id=erp_account_id', array('customer_account_name' => 'name'), null, 'left');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn(
                'id', array(
            'header' => Mage::helper('epicor_comm')->__('Web Returns Number'),
            'align' => 'center',
            'index' => 'id',
                )
        );

        $this->addColumn(
                'status', array(
            'header' => Mage::helper('epicor_comm')->__('Erp Returns Number'),
            'index' => 'erp_returns_number',
                )
        );

        $this->addColumn(
                'customer_reference', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Ref'),
            'index' => 'customer_reference',
                )
        );

        $this->addColumn(
                'customer_short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Erp Account'),
            'index' => 'customer_account_name',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Sales_Returns_Renderer_Erpaccount(),
                )
        );

        $this->addColumn(
                'email_address', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Email'),
            'index' => 'email_address',
                )
        );

        $this->addColumn(
                'customer_name', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Name'),
            'index' => 'customer_name',
                )
        );

        $this->addColumn(
                'rma_case_number', array(
            'header' => Mage::helper('epicor_comm')->__('Case Number'),
            'index' => 'rma_case_number',
                )
        );

        $this->addColumn(
                'rma_contact', array(
            'header' => Mage::helper('epicor_comm')->__('Contact'),
            'index' => 'rma_contact',
                )
        );

        $this->addColumn(
                'returns_status', array(
            'header' => Mage::helper('epicor_comm')->__('Status'),
            'index' => 'returns_status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Sales_Returns_Renderer_Status(),
                )
        );

        $this->addColumn(
                'rma_date', array(
            'header' => Mage::helper('epicor_comm')->__('Created'),
            'index' => 'rma_date',
            'align' => 'center',
            'type' => 'date',
                )
        );

        $this->addColumn(
                'action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View'),
                    'url' => array('base' => '*/*/view'),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
                )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

}
