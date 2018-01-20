<?php

/**
 * Customer  list Grid config
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';
    
    protected $_erp_customer;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('listgrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setNoFilterMassactionColumn(true);
        // $this->setCustomData($collection->getItems());
    }
    
    protected function _prepareCollection()
    {
        $erpAccount = Mage::helper('epicor_comm')->getErpAccountInfo();
        $customerSession = Mage::getSingleton('customer/session')->getCustomer(); 
        $isMasterShopper = $customerSession->getData('ecc_master_shopper');   
        $customerId = $customerSession->getData('entity_id');
        $customerAccountType = $customerSession->getEccErpAccountType();        
        $collection = Mage::getModel('epicor_lists/list')->getCollection();
        // dont display lists which are excluded  
        $collection->addFieldToFilter('erp_accounts_exclusion', array('eq' => 'N'));     
        $collection->getSelect()->joinLeft(array(
            'lea' => $collection->getTable('epicor_lists/list_erp_account')
        ), 'lea.list_id=main_table.id', array(
            'lea.erp_account_id'
        ));
        $erpAccountId = $erpAccount->getId();
        $this->_masterShopperList($collection,$erpAccountId);        
        if($customerAccountType !="guest") {
            $collection->addFieldToFilter('lea.erp_account_id', $erpAccount->getId());
        }
        //only sees lists with a list type of pre-defined or favourite or product group
        $collection->addFieldToFilter('type',array('in' => array('Pl','Fa','Pg')));
        $needle="M";
        if($isMasterShopper) {
            //Lists assigned to their ERP Account with a source of “customer”
            //Lists assigned to their ERP Account with a source of “web” that meet certain criteria (Non Mandatory (i.e. does not have the “M” setting))
            $collection->getSelect()->where(new Zend_Db_Expr("(source IN('customer')) OR (source = 'web' AND settings NOT LIKE '%M%' )"));
        } else {
           $collection->addFieldToFilter('source',array('eq' => array('customer')));       
           $collection->addFieldToFilter(array('owner_id', 'settings'),array(array('eq' => $customerId),array('nlike' => '%'.$needle.'%')));
        }
        $collection->getSelect()->group('lea.list_id');
      
        $this->setCollection($collection);        
   
        return parent::_prepareCollection();
    }
    
    protected function _masterShopperList($collection,$erpAccountId)
    {
        $customerSession = Mage::getSingleton('customer/session')->getCustomer(); 
        $isMasterShopper = $customerSession->getData('ecc_master_shopper');   
        $customerId = $customerSession->getData('entity_id');
        //A master shopper only sees (and can only amend and delete) lists with a list type of pre-defined or favourite 
        //and which are assigned to his ERP Account and no other ERP Account. 
        if($isMasterShopper) {
            $subquery = new Zend_Db_Expr('SELECT lea.list_id FROM epicor_lists_list_erp_account AS lea WHERE lea.list_id = main_table.id AND lea.erp_account_id <> "'.$erpAccountId.'"');
            $collection->addFieldToFilter('lea.list_id', array('nin' => array($subquery)));  
        } else {
            // A non master shopper/Registered shopper/Registered Guest 
            // only sees (and can only amend and delete) lists with a list type of pre-defined or favourite 
            // and which are assigned to his ERP Account and customer and no other ERP Account / customer 
            $tableJoin = array('customer' => $collection->getTable('epicor_lists/list_customer')); 
            $tableCols = array('customer.customer_id' => 'customer_id');                       
            $collection->getSelect()->joinLeft($tableJoin, 'main_table.id = customer.list_id', $tableCols);   
 
            //retrieve customers other than the current customer for a list
            // but remove any lists that have the current customer on it 
            $customerSub = new Zend_Db_Expr('SELECT lc.list_id FROM epicor_lists_list_customer AS lc WHERE lc.list_id = main_table.id AND lc.customer_id <> "'.$customerId.'" and lc.list_id <> ('
                                   . 'SELECT ld.list_id FROM epicor_lists_list_customer AS ld WHERE ld.list_id = main_table.id AND ld.customer_id = "'.$customerId.'")');
            
            
            // remove from collection any lists returned from above condition 
            $collection->addFieldToFilter('lea.list_id', array('nin' => array($customerSub))); 
             
            //$subquery = new Zend_Db_Expr('SELECT customer.list_id FROM epicor_lists_list_customer AS customer WHERE customer.list_id = main_table.id AND customer.customer_id <> "'.$customerId.'"');
            //$collection->addFieldToFilter('lea.list_id', array('nin' => array($subquery)));  
            $subqueryErp = new Zend_Db_Expr('SELECT lea.list_id FROM epicor_lists_list_erp_account AS lea WHERE lea.list_id = main_table.id AND lea.erp_account_id <> "'.$erpAccountId.'"');
            $collection->addFieldToFilter('lea.list_id', array('nin' => array($subqueryErp)));            
        }
 
        return $collection;         
    }     
    
    
    
    
    protected function _prepareLayout()
    {
        
        $urlRedirect = $this->getUrl('*/*/new', array(
            '_current' => true,
            'contract' => $this->getRequest()->getParam('contract')
        ));
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('adminhtml')->__('Add New List'),
            'onclick' => "location.href='$urlRedirect';",
            'class' => 'task'
        )));
        
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
    
    protected function _toHtml()
    {
        $html = parent::_toHtml(false);
        return $html;
    }
    
    public function getRowUrl($row)
    {
        //return $this->getUrl('*/*/edit', array(
        //    'id' => base64_encode($row->getId())
        //));
    }
    
    protected function _prepareColumns()
    {
        $helper    = Mage::helper('epicor_lists');
        $typeModel = Mage::getModel('epicor_lists/list_type');
        $customerSession = Mage::getSingleton('customer/session')->getCustomer(); 
        $isMasterShopper = $customerSession->getData('ecc_master_shopper');         
        
//        $this->addColumn('id', array(
//            'header' => $this->__('ID'),
//            'index' => 'id',
//            //'width'    => '450px',
//            'column_css_class' => 'no-display',
//            'header_css_class' => 'no-display'
//        ));

        $this->addColumn('erp_code', array(
            'header' => $this->__('Reference Code'),
            'index' => 'erp_code',
            'type' => 'text'
        ));        
        
        $this->addColumn('type', array(
            'header' => $this->__('Type'),
            //'width' => '350px',
            'index' => 'type',
            'type' => 'options',
            'options' => $typeModel->toListFilterArray()
        ));
        
        $this->addColumn('title', array(
            'header' => $this->__('Title'),
            'index' => 'title',
            'type' => 'text'
        ));
        
        $this->addColumn('start_date', array(
            'header' => $this->__('Start Date'),
            'index' => 'start_date',
            //'width'    => '350px',
            'type' => 'datetime'
        ));
        
        $this->addColumn('end_date', array(
            'header' => $this->__('End Date'),
            'index' => 'end_date',
            //'width'    => '350px',
            'type' => 'datetime'
        ));
        
        $this->addColumn('status', array(
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
            'filter_condition_callback' => array(
                $this,
                '_statusFilter'
            )
        ));
        
        $this->addColumn('active', array(
            'header' => $this->__('Active'),
            'index' => 'active',
            'type' => 'options',
            'options' => array(
                0 => $helper->__('No'),
                1 => $helper->__('Yes')
            )
        ));
        
        if($isMasterShopper) {
            $this->addColumn('owner_id', array(
                'header' => $this->__('Created By'),
                'index' => 'owner_id',
                'type' => 'text',
                'filter' => false,
                'sortable'  => false,
                'renderer' => new Epicor_Lists_Block_Customer_Account_List_Renderer_Createdby,
            ));            
        }
        
        
        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100px',
            'renderer' => new Epicor_Lists_Block_Customer_Account_List_Renderer_Editlist,
            'links' => 'true',
            'getter' => 'getId',
            'sortable'  => false,
            'filter' => false,
            'column_css_class' => 'a-center ',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    
                    'id' =>'edit',
                    'field' => 'id'
                ), 
                array(
                    'caption' => $this->__(' | '),
                    'id' =>'separator',
                ),                                
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array(
                        'base' => '*/*/delete'
                    ),
                    'field' => 'id',
                    'id' =>'delete',
                    'confirm' => $this->__('Are you sure you want to delete this List? This cannot be undone')
                ),
                
            )
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('listid');
        
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_lists')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_lists')->__('Delete selected Lists?')
        ));
        $customers    = Mage::helper('epicor_comm')->getErpAccountInfo()->getCustomers();
               // ->addFieldToFilter('entity_id', array('nin' => Mage::getSingleton('customer/session')->getId()));
        $customers->addAttributeToFilter('store_id', $storeId);
        
        $customerData = array();
        foreach ($customers as $filtered_customer) {
            $customerData[] = array(
                'value' => $filtered_customer->getId(),
                'label' => $filtered_customer->getName()
            );
        }

        $customerSession = Mage::getSingleton('customer/session')->getCustomer(); 
        $isMasterShoper = $customerSession->getData('ecc_master_shopper');
        
        if ((count($customerData) >= 1) && ($isMasterShoper)) {
            $this->getMassactionBlock()->addItem('assigncustomer', array(
                'label' => Mage::helper('epicor_lists')->__('Assign Customer'),
                'url' => $this->getUrl('*/*/massAssignCustomer'),
                'additional' => array(
                    'sales_rep_account' => array(
                        'name' => 'assign_customer',
                        'type' => 'select',
                        'values' => $customerData,
                        'label' => Mage::helper('customer')->__('Customer')
                    )
                )
            ));
            
            
            $this->getMassactionBlock()->addItem('removecustomer', array(
                'label' => Mage::helper('epicor_lists')->__('Remove Customer'),
                'url' => $this->getUrl('*/*/massRemoveCustomer'),
                'additional' => array(
                    'sales_rep_account' => array(
                        'name' => 'remove_customer',
                        'type' => 'select',
                        'values' => $customerData,
                        'label' => Mage::helper('customer')->__('Customer')
                    )
                )
            ));
        }
        
        return $this;
    }
    
    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/listgrid', array(
            '_current' => true
        ));
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
                $collection->addFieldToFilter('end_date', array(
                    'lteq' => now()
                ));
                break;
            
            case self::LIST_STATUS_PENDING:
                $collection->addFieldToFilter('active', 1);
                $collection->addFieldToFilter('start_date', array(
                    'gteq' => now()
                ));
                break;
        }
        
        return $this;
    }
    
}