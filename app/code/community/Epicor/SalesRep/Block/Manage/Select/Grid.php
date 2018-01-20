<?php

/**
 * Branchpickup select page grid
 *
 * @category   Epicor
 * @package    Epicor_Branchpickup
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Manage_Select_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();
    protected $_salesrepChildrenIds;

    public function __construct()
    {
        parent::__construct();
        $this->setId('masqueradegrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('erp_account_name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareLayout()
    {

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */
        $masquerade = $customerSession->getMasqueradeAccountId();
        if($masquerade) {
            $helper = Mage::helper('epicor_salesrep');
            /* @var $helper Epicor_SalesRep_Helper_Data */
            $isSecure = $helper->isSecure();
            $redirectUrl = Mage::getUrl('salesrep/account/index', array('_forced_secure' => $isSecure));
            $returnUrl = Mage::helper('epicor_comm')->urlEncode($redirectUrl);
            $ajax_url  = Mage::getUrl('comm/masquerade/masquerade', array('_forced_secure' => $isSecure,'return_url' => $returnUrl));
            //$ajax_url = Mage::getBaseUrl().'comm/masquerade/masquerade?return_url='.$returnUrl;       
            $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                    'label' => Mage::helper('adminhtml')->__('End Masquerade'),
                    'onclick' => "location.href='$ajax_url';",
                    'class' => 'task'
            )));
        }
        return parent::_prepareLayout();
    }


    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * Mage_Adminhtml_Block_Widget_Grid
     */
    
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html .= $this->getAddButtonHtml();
        return $html;
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_erpaccounts') {
            $ids = $this->_getSelected();
            if (!empty($ids)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array(
                        'in' => $ids
                    ));
                } else {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array(
                        'nin' => $ids
                    ));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Location
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $helper = Mage::helper('epicor_salesrep/account_manage');
            /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

            $salesRep        = $helper->getManagedSalesRepAccount();
            $this->_salesrep = $salesRep;
        }

        return $this->_salesrep;
    }

    public function getSalesRepAccountChildrenIds()
    {
        if (!$this->_salesrepChildrenIds) {

            $salesRep                   = $this->getSalesRepAccount();
            $this->_salesrepChildrenIds = $salesRep->getHierarchyChildAccountsIds();
        }

        return $this->_salesrepChildrenIds;
    }

    /**
     * 
     * @return type
     */
    protected function _prepareCollection()
    {
        $collection  = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */
        $helper      = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        $salesRep    = $helper->getBaseSalesRepAccount();
        $erpAccounts = $salesRep->getMasqueradeAccountIds();
        $collection->addFieldToFilter('main_table.entity_id', array(
            'in' => $erpAccounts
        ));
        
        if ($this->isColumnEnabled('invoice_address')) {
            
            $columns = array(
                'invoice_name' => 'name',
                'invoice_address1' => 'address1',
                'invoice_address2' => 'address2',
                'invoice_address3' => 'address3',
                'invoice_city' => 'city',
                'invoice_county' => 'county',
                'invoice_country' => 'country',
                'invoice_postcode' => 'postcode',
                'invoice_address_full' => 'CONCAT_WS(\' \', invoice_address.name, invoice_address.address1, invoice_address.address2, invoice_address.address3, invoice_address.city, invoice_address.county, invoice_address.country, invoice_address.postcode)',
            );

            $table = $collection->getTable('epicor_comm/customer_erpaccount_address');
            $collection->getSelect()->joinLeft(array('invoice_address' => $table), 'invoice_address.erp_code = main_table.default_invoice_address_code AND invoice_address.erp_customer_group_code = main_table.erp_code ', $columns, null, 'left');
        }
        
        if ($this->isColumnEnabled('default_shipping_address')) {
            
            $columns = array(
                'shipping_name' => 'name',
                'shipping_address1' => 'address1',
                'shipping_address2' => 'address2',
                'shipping_address3' => 'address3',
                'shipping_city' => 'city',
                'shipping_county' => 'county',
                'shipping_country' => 'country',
                'shipping_postcode' => 'postcode',
                'shipping_address_full' => 'CONCAT_WS(\' \', shipping_address.name, shipping_address.address1, shipping_address.address2, shipping_address.address3, shipping_address.city, shipping_address.county, shipping_address.country, shipping_address.postcode)',
            );
            
            $table = $collection->getTable('epicor_comm/customer_erpaccount_address');
            $collection->getSelect()->joinLeft(array('shipping_address' => $table), 'shipping_address.erp_code = main_table.default_delivery_address_code AND shipping_address.erp_customer_group_code = main_table.erp_code ', $columns, null, 'left');
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $this->addColumn('erp_account_name', array(
            'header' => Mage::helper('core')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'main_table.name',
            'width' => '250px'
        ));
        
        if ($this->isColumnEnabled('account_number')) {
            $this->addColumn('account_number', array(
                'header' => Mage::helper('epicor_comm')->__('Account Number'),
                'align' => 'left',
                'index' => 'account_number',
                'condition' => 'LIKE',
                'filter_index' => 'main_table.account_number'
                )
            );
        }

        if ($this->isColumnEnabled('short_code')) {
            $this->addColumn('short_code', array(
                'header' => Mage::helper('epicor_comm')->__('Short Code'),
                'align' => 'left',
                'index' => 'short_code',
                'type' => 'text',
                'condition' => 'LIKE',
                'filter_index' => 'main_table.short_code',
            'width' => '150px'
                )
            );
        }

        if ($this->isColumnEnabled('invoice_address')) {
            $this->addColumn('invoice_address', array(
                'header' => Mage::helper('epicor_comm')->__('Invoice Address'),
                'align' => 'left',
                'index' => 'invoice_address_full',
                'type' => 'text',
                'condition' => 'LIKE',
                'filter_condition_callback' => array($this, '_filterInvoice'),
                'renderer' => new Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Invoice(),
                )
            );
        }

        if ($this->isColumnEnabled('default_shipping_address')) {
            $this->addColumn('default_shipping_address', array(
                'header' => Mage::helper('epicor_comm')->__('Default Shipping Address'),
                'align' => 'left',
                'index' => 'shipping_address_full',
                'type' => 'text',
                'condition' => 'LIKE',
                'filter_condition_callback' => array($this, '_filterShipping'),
                'renderer' => new Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Shipping(),
                )
            );
        }

        $this->addColumn('select', array(
            'header' => $this->__('Masquerade as'),
            'width' => '140',
            'index' => 'id',
            'renderer' => new Epicor_SalesRep_Block_Manage_Select_Grid_Renderer_Select(),
            'links' => 'true',
            'getter' => 'getId',
            'header-align' => 'center',
            'align' => 'center',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Begin Masquerade'),
                    'url' => '',
                    'onclick' => 'selectMasquerade(this); return false;',
                    'id' =>'return'
                )
            )
        ));

        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));
        return parent::_prepareColumns();
    }

    protected function _getSelected()
    // Used in grid to return selected customers values.
    {
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $salesRep      = $this->getSalesRepAccount();
            $erpAccountIds = $salesRep->getErpAccountIds();
            foreach ($erpAccountIds as $erpAccountId) {
                $this->_selected[$erpAccountId] = array(
                    'entity_id' => $erpAccountId
                );
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array(
                    'id' => $id
                );
            }
        }
    }

    public function getGridUrl()
    {

        $helper = Mage::helper('epicor_salesrep');
        /* @var $helper Epicor_SalesRep_Helper_Data */
        $isSecure = $helper->isSecure();

        $params = array(
            'id' => $this->getSalesRepAccount()->getId(),
            '_current' => true,
            '_forced_secure' => $isSecure
        );
        return $this->getUrl('*/*/masqueradegrid', $params);
    }

    public function getRowUrl($row)
    {
        return null;
    }

    protected function _toHtml()
    {

        $html = parent::_toHtml();

        return $html;
    }

    protected function _salesRepAccountFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $salesRepIds = $this->getSalesRepAccountChildrenIds();

        $this->getCollection()->join(array(
            'salesrep_erp' => 'epicor_salesrep/erpaccount'
            ), 'main_table.entity_id = salesrep_erp.erp_account_id', '')->join(array(
            'salesrep' => 'epicor_salesrep/account'
            ), 'salesrep.id = salesrep_erp.sales_rep_account_id', '');
        $this->getCollection()->getSelect()->group('main_table.entity_id');

        if (strtolower($value) == strtolower($this->__('This account'))) {
            $salesRepAccount   = $this->getSalesRepAccount();
            /* @var $salesRepAccount Epicor_SalesRep_Model_Account */
            $salesRepAccountId = $salesRepAccount->getId();
            $this->getCollection()->addFieldtoFilter('salesrep.id', $salesRepAccountId);
            
        } elseif (strtolower($value) == strtolower($this->__('Child account'))) {
            $childrenSalesRepAccountsIds = $this->getSalesRepAccountChildrenIds();
            $this->getCollection()->addFieldtoFilter('salesrep.id', array(
                'in' => $childrenSalesRepAccountsIds
            ));
            
        } elseif (strtolower($value) == strtolower($this->__('Multiple accounts'))) {
            $childrenSalesRepAccountsIds = $this->getSalesRepAccountChildrenIds();
            $this->getCollection()->addFieldtoFilter('salesrep.id', array(
                'in' => $childrenSalesRepAccountsIds
            ));
            $this->getCollection()->getSelect()->having('COUNT(*) > 1');
            
        } else {
            $this->getCollection()->addFieldtoFilter('salesrep.name', array(
                'like' => "%$value%"
            ));
        }

        return $this;
    }

    public function isColumnEnabled($column)
    {
        return Mage::getStoreConfig('epicor_salesrep/masquerade_search/' . $column);
    }

    /**
     * Filter Invoice condition
     * 
     * @param Epicor_Comm_Model_Mysql4_Location_Product_Collection $collection
     * @param type $column
     * 
     * @return void
     */
    public function _filterInvoice($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $collection->getSelect()->where(
            'CONCAT_WS(\' \', invoice_address.name, invoice_address.address1, invoice_address.address2, invoice_address.address3, invoice_address.city, invoice_address.county, invoice_address.country, invoice_address.postcode) LIKE ?',
            '%' . $value . '%'
        );
        
    }
    
    /**
     * Filter Invoice condition
     * 
     * @param Epicor_Comm_Model_Mysql4_Location_Product_Collection $collection
     * @param type $column
     * 
     * @return void
     */
    public function _filterShipping($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $collection->getSelect()->where(
            'CONCAT_WS(\' \', shipping_address.name, shipping_address.address1, shipping_address.address2, shipping_address.address3, shipping_address.city, shipping_address.county, shipping_address.country, shipping_address.postcode) LIKE ?',
            '%' . $value . '%'
        );
    }
    
}
