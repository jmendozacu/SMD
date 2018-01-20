<?php

/**
 * This class is responsible for setting template in newly created list tab under customer edit section
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_Customer_Edit_Tab_Lists extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_list');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('in_custlist' => 1));
        $this->setSkipGenerateContent(true);
    }

    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/epicorlists_customer/lists', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    protected function _prepareCollection()
    {   
        $customerDetails     = Mage::registry('current_customer');
        $erpAccountId        = $customerDetails->getErpaccountId();
        $account             = Mage::getModel('epicor_comm/customer_erpaccount')->load($erpAccountId);
        $erpAccountType      = $account->getAccountType();
        $customerAccountType = $customerDetails->getEccErpAccountType();
        $guestSaleRep        = array('guest','salesrep');
        if(in_array($customerAccountType, $guestSaleRep)) {
            $typeFilter = $customerAccountType == 'salesrep' ? array('B','C','E','N') : array('E','N');
        } else {
            $typeFilter = $erpAccountType == 'B2B' ? array('B','E','N') : array('C','E','N');
        }
        $collection   = Mage::getModel('epicor_lists/list')->getCollection();
        $collection->addFilterToMap('id', 'main_table.id');
        $collection->getSelect()->joinLeft(
            array('lea' => $collection->getTable('epicor_lists/list_erp_account')), 'lea.list_id=main_table.id', array('lea.erp_account_id')
        );
        $collection->addFieldToFilter('main_table.erp_account_link_type', array('in' =>$typeFilter));
        //if the customer account type is customer(not guest/salesrep) 
        // need to show lists that have a link type of "B2B" / "B2C" that have no erp accounts
        // if the customer's ERP Account matches that type
        if(!in_array($customerAccountType, $guestSaleRep)) {
            $collection->addFieldToFilter(array('lea.erp_account_id', 'lea.erp_account_id'),array(array('eq'=>$erpAccountId),array('null'=>'true')));
        } else if($customerAccountType =="guest") { // if the customer account type is guest then check in N and E(that have no erp accounts)
            $collection->addFieldToFilter(array('lea.erp_account_id'),array(array('null'=>'true')));
        } 
        $collection->addFieldToFilter('main_table.type', array('neq'=> 'Co'));
        $collection->getSelect()->group('main_table.id');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $customer = Mage::getModel('customer/customer')->load(Mage::registry('current_customer')->getId());
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            foreach ($customer->getLists() as $clist) {
                $this->_selected[$clist->getId()] = array('id' => $clist->getId());
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected)
    {
        // print_r($selected);die;
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_custlist') {

            $productIds = $this->_getSelected();
            if (empty($productIds)) {
                $productIds = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_custlist', array(
            'header' => Mage::helper('epicor_lists')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_custlist',
            'align' => 'center',
            'index' => 'id',
            'sortable' => false,
            'field_name' => 'links[]',
            'values' => $this->_getSelected(),
        ));
        $typeModel = Mage::getModel('epicor_lists/list_type');
        $this->addColumn('type', array(
            'header' => Mage::helper('epicor_lists')->__('Type'),
            'width' => '150',
            'index' => 'type',
            'type' => 'options',
            'options' => $typeModel->toFilterArray()
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('epicor_lists')->__('Title'),
            'width' => '150',
            'index' => 'title',
            'filter_index' => 'title'
        ));

        $this->addColumn('lists_erp_code', array(
            'header' => Mage::helper('epicor_lists')->__('Erp Code'),
            'width' => '150',
            'index' => 'erp_code',
            'filter_index' => 'erp_code'
        ));
        $this->addColumn(
                'status', array(
            'header' => Mage::helper('epicor_lists')->__('Current Status'),
            'index' => 'active',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'renderer' => 'epicor_lists/adminhtml_widget_grid_column_renderer_active',
            'type' => 'options',
            'options' => array(
                self::LIST_STATUS_ACTIVE => Mage::helper('epicor_lists')->__('Active'),
                self::LIST_STATUS_DISABLED => Mage::helper('epicor_lists')->__('Disabled'),
                self::LIST_STATUS_ENDED => Mage::helper('epicor_lists')->__('Ended'),
                self::LIST_STATUS_PENDING => Mage::helper('epicor_lists')->__('Pending')
            ),
            'filter_condition_callback' => array($this, '_statusFilter'),
                )
        );

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    public function _statusFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        switch ($value) {
            case self::LIST_STATUS_ACTIVE:
                $collection->addFieldToFilter('active', 1);
                $collection->addFieldToFilter('start_date', array(
                    array('lteq' => now()),
                    array('null' => 1),
                    array('eq' => '0000-00-00 00:00:00'),
                ));

                $collection->addFieldToFilter('end_date', array(
                    array('gteq' => now()),
                    array('null' => 1),
                    array('eq' => '0000-00-00 00:00:00'),
                ));
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

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Lists';
    }

    public function getTabTitle()
    {
        return 'Lists';
    }

    public function isHidden()
    {
        $customer = Mage::registry('current_customer');
        //If the customer type is supplier, then hide the Lists tabs in Admin->customer->edit
        if($customer->getEccErpAccountType() =="supplier"){
            return true;
        } else {
           return false; 
        }
    }

    public function getGridUrl()
    {
        $customer = Mage::registry('current_customer');
        $params = array(
            'id' => $customer->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('*/epicorlists_customer/listsgrid', $params);
    }

}
