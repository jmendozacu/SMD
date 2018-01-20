<?php

/**
 * Erp account Lists grid
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Lists extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_erp_customer;
    private $_selected = array();
    private $_erpSystem = array('p21');

    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';

    public function __construct()
    {
        parent::__construct();
        $this->setId('erpaccount_lists');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('lists' => 1));
        $this->setSaveParametersInSession(true);
        $checkEditable = $this->checkErp();
        if(!$checkEditable) {
            //Global Contract Settings should not be amendable on ERP Accounts on Customer if ECC is linked to ERP "P21"
            $this->setAdditionalJavaScript("document.getElementById('allowed_contract_type').disabled=true;
                                            document.getElementById('required_contract_type').disabled=true;
                                            document.getElementById('allow_non_contract_items').disabled=true;");     
        }         
    }
    
    public function checkErp()
    {
        $erp = Mage::getStoreConfig('Epicor_Comm/licensing/erp');
        if(in_array($erp, $this->_erpSystem)) {
            $editable = false;
        } else {
            $editable = true;
        }
        return $editable;
    }    

    public function getErpCustomer()
    {
        if (!$this->_erp_customer) {
            if (Mage::registry('customer_erp_account')) {
                $this->_erp_customer = Mage::registry('customer_erp_account');
            } else {
                $this->_erp_customer = Mage::getModel('epicor_comm/customer_erpaccount')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->_erp_customer;
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
        return false;
    }

    protected function _prepareCollection()
    {
        $erpAccountType = $this->getErpCustomer()->getAccountType();
        switch ($erpAccountType) {
            case "B2B":
                $ercAccountLinkType ="B";
                break;
            case "B2C":
                $ercAccountLinkType ="C";
                break;
        }        
       
        $collection = Mage::getModel('epicor_lists/list')->getCollection()
                ->addFieldToFilter('erp_account_link_type', array($ercAccountLinkType, 'E'))
                ->addFieldToFilter('type', array('neq'=> 'Co'));
                
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $collection = Mage::getModel('epicor_lists/list_erp_account')->getCollection()->addFieldToFilter('erp_account_id', $this->getErpCustomer()->getId());
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            foreach ($collection->getData() as $listData) {
                $this->_selected[$listData['list_id']] = array('id' => $listData['list_id']);
            }
        }

        return $this->_selected;
    }

    public function setSelected($selected)
    {

        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'lists') {

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
        $this->addColumn('lists', array(
            'header' => Mage::helper('epicor_lists')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'lists',
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
            'header' => Mage::helper('epicor_lists')->__('Erp code'),
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

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/listsgrid', $params);
    }

}
