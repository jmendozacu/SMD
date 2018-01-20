<?php

/**
 * List admin actions
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';

    public function __construct()
    {
        parent::__construct();
        $this->setId('list_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_lists/list')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */


        $this->addColumn(
                'id', array(
            'header' => $helper->__('ID'),
            'index' => 'id',
            'type' => 'number'
                )
        );

        $typeModel = Mage::getModel('epicor_lists/list_type');
        /* @var $typeModel Epicor_Lists_Model_List_Type */

        $this->addColumn(
                'type', array(
            'header' => $helper->__('Type'),
            'index' => 'type',
            'type' => 'options',
            'options' => $typeModel->toFilterArray()
                )
        );

        $this->addColumn(
                'title', array(
            'header' => $helper->__('Title'),
            'index' => 'title',
            'type' => 'text'
                )
        );

        $this->addColumn(
            'erp_code', array(
            'header' => $helper->__('ERP Code'),
            'index' => 'erp_code',
            'type' => 'text',
            'renderer' => new Epicor_Lists_Block_Adminhtml_List_Grid_Renderer_Erpcode()
            )
        );

        $this->addColumn(
                'start_date', array(
            'header' => $helper->__('Start Date'),
            'index' => 'start_date',
            'type' => 'datetime'
                )
        );

        $this->addColumn(
                'end_date', array(
            'header' => $helper->__('End Date'),
            'index' => 'end_date',
            'type' => 'datetime'
                )
        );

        $this->addColumn(
                'active', array(
            'header' => $helper->__('Active'),
            'index' => 'active',
            'type' => 'options',
            'options' => array(
                0 => $helper->__('No'),
                1 => $helper->__('Yes')
            )
                )
        );

        $this->addColumn(
                'status', array(
            'header' => $helper->__('Current Status'),
            'index' => 'active',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_active',
            'type' => 'options',
            'options' => array(
                self::LIST_STATUS_ACTIVE => $helper->__('Active'),
                self::LIST_STATUS_DISABLED => $helper->__('Disabled'),
                self::LIST_STATUS_ENDED => $helper->__('Ended'),
                self::LIST_STATUS_PENDING => $helper->__('Pending')
            ),
            'filter_condition_callback' => array($this, '_statusFilter'),
                )
        );

        $this->addColumn(
                'source', array(
            'header' => $helper->__('Source'),
            'index' => 'source',
            'type' => 'text'
                )
        );

        $this->addColumn('action', array(
            'header' => $helper->__('Action'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $helper->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => $helper->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => $helper->__('Are you sure you want to delete this List? This cannot be undone')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('listid');
        $helper = Mage::helper('epicor_lists');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_lists')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_lists')->__('Delete selected Lists?')
        ));

        $this->getMassactionBlock()->addItem('assignerpaccount', array(
            'label' => Mage::helper('epicor_lists')->__('Assign ERP Account'),
            'url' => $this->getUrl('*/*/massAssignErpAccount'),
            'additional' => array(
                'ecc_erp_account_type' => array(
                    'name' => 'assign_erp_account',
                    'type' => 'account_selector',
                    'renderer' => array(
                        'type' => 'account_selector',
                        'class' => 'Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount'
                    ),
                    'label' => Mage::helper('customer')->__('Assign Account'),
                    'required' => true
                ),
            )
        ));

        $this->getMassactionBlock()->addItem('assigncustomer', array(
            'label' => Mage::helper('epicor_lists')->__('Assign Customer'),
            'url' => $this->getUrl('*/*/massAssignCustomer'),
            'additional' => array(
                'sales_rep_account' => array(
                    'name' => 'assign_customer',
                    'type' => 'customer_selector',
                    'renderer' => array(
                        'type' => 'customer_selector',
                        'class' => 'Epicor_Comm_Block_Adminhtml_Form_Element_Customer'
                    ),
                    'label' => Mage::helper('customer')->__('Customer'),
                    'required' => true
                )
            )
        ));

        $this->getMassactionBlock()->addItem('removeerpaccount', array(
            'label' => Mage::helper('epicor_lists')->__('Remove ERP Account'),
            'url' => $this->getUrl('*/*/massRemoveErpAccount'),
            'additional' => array(
                'ecc_erp_account_type' => array(
                    'name' => 'remove_erp_account',
                    'type' => 'account_selector',
                    'renderer' => array(
                        'type' => 'account_selector',
                        'class' => 'Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount'
                    ),
                    'label' => Mage::helper('customer')->__('Assign Account'),
                    'required' => true
                )
            )
        ));

        $this->getMassactionBlock()->addItem('removecustomer', array(
            'label' => Mage::helper('epicor_lists')->__('Remove Customer'),
            'url' => $this->getUrl('*/*/massRemoveCustomer'),
            'additional' => array(
                'sales_rep_account' => array(
                    'name' => 'remove_customer',
                    'type' => 'customer_selector',
                    'renderer' => array(
                        'type' => 'customer_selector',
                        'class' => 'Epicor_Comm_Block_Adminhtml_Form_Element_Customer'
                    ),
                    'label' => Mage::helper('customer')->__('Customer'),
                    'required' => true
                )
            )
        ));


        $status_data = array('1' => $helper->__('Active'), '0' => $helper->__('Disabled'));

        $this->getMassactionBlock()->addItem('changestatus', array(
            'label' => Mage::helper('epicor_lists')->__('Change Status'),
            'url' => $this->getUrl('*/*/massAssignStatus'),
            'additional' => array(
                'list_status' => array(
                    'name' => 'assign_status',
                    'type' => 'select',
                    'values' => $status_data,
                    'label' => Mage::helper('customer')->__('Change Status'),
                )
            )
        ));



        return $this;
    }

    public function _statusFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        switch ($value) {
            case self::LIST_STATUS_ACTIVE:
                $collection->filterActive();
                break;

            case self::LIST_STATUS_DISABLED:
                $collection->addFieldToFilter('active', 0);
                break;

            case self::LIST_STATUS_ENDED:
                $collection->addFieldToFilter('active', 1);
                $collection->addFieldToFilter('end_date', array('lteq' => now()));
                break;

            case self::LIST_STATUS_PENDING:
                $collection->addFieldToFilter('active', 1);
                $collection->addFieldToFilter('start_date', array('gteq' => now()));
                break;
        }

        return $this;
    }

}
