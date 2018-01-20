<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('quotesgrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('quotes/quote')->getCollection();
        /* @var $collection Epicor_Quotes_Model_Mysql4_Quote_Collection */

        $collection->joinQuoteCustomerTable()
            ->addCustomerInfoSelect()
            ->joinErpAccountTable()
            ->getSelect()->group('main_table.entity_id');
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn(
            'entity_id',
            array(
            'header' => Mage::helper('quotes')->__('Id'),
            'align' => 'center',
            'index' => 'entity_id',
            'width' => '70px',
            'filter_index' => 'main_table.entity_id',
            )
        );
        
        $request = Mage::getStoreConfigFlag('epicor_comm_enabled_messages/gqr_request/active');
        $upload = Mage::getStoreConfigFlag('epicor_comm_field_mapping/gqr_mapping/active');
        
        if ($upload || $request) {
            $this->addColumn(
                'quote_number',
                array(
                'header' => Mage::helper('quotes')->__('ERP Quote Number'),
                'align' => 'center',
                'index' => 'quote_number',
                'width' => '70px',
                )
            );
        }

        $this->addColumn(
            'customer_info',
            array(
            'header' => Mage::helper('quotes')->__('Customer'),
            'index' => 'customer_info',
            'filter_index' => "CONCAT(IFNULL(`cfirst`.`value`,''), ' ',IFNULL(`clast`.`value`,''),' (',`c`.`email`,')')"
            )
        );

        $this->addColumn(
            'customer_erp_code',
            array(
            'header' => Mage::helper('quotes')->__('ERP Account'),
            'index' => 'customer_short_code',
            'filter_index' => 'erp.short_code'
            )
        );

        $this->addColumn(
            'currency_code',
            array(
            'header' => Mage::helper('quotes')->__('Currency Code'),
            'index' => 'currency_code',
            )
        );

        $this->addColumn(
            'is_global',
            array(
            'header' => Mage::helper('quotes')->__('Global for ERP Account'),
            'index' => 'is_global',
            'type' => 'options',
            'options' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            )
        );

        $this->addColumn(
            'status',
            array(
            'header' => Mage::helper('quotes')->__('Status'),
            'index' => 'status_id',
            'type' => 'options',
            'width' => '150px',
            'options' => Mage::getModel('quotes/quote')->getQuoteStatuses()
            )
        );

        $this->addColumn(
            'expires',
            array(
            'header' => Mage::helper('quotes')->__('Expires'),
            'index' => 'expires',
            'align' => 'center',
            'type' => 'date',
            'width' => '80px',
            )
        );

        $this->addColumn(
            'created_at',
            array(
            'header' => Mage::helper('quotes')->__('Created'),
            'index' => 'created_at',
            'align' => 'center',
            'type' => 'date',
            'width' => '80px',
            'filter_index' => 'main_table.created_at',
            )
        );

        $this->addColumn(
            'updated_at',
            array(
            'header' => Mage::helper('quotes')->__('Last Updated'),
            'align' => 'center',
            'index' => 'updated_at',
            'type' => 'date',
            'width' => '80px',
            'filter_index' => 'main_table.updated_at',
            )
        );



        $this->addColumn(
            'action',
            array(
            'header' => Mage::helper('quotes')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('quotes')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
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
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
