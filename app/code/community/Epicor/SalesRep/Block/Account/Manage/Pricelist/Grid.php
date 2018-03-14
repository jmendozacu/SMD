<?php

/**
 * Customer  list Grid config
 * 
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Salesrep_Block_Account_Manage_Pricelist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';
    
    protected $_erp_customer;
    protected $_test;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('pricelistgrid');
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
        $salesRep    = Mage::helper('epicor_salesrep/account_manage')->getCustomerSalesRepAccount();
        $erpAccounts = $salesRep->getMasqueradeAccountIds(); 
        
        //get Shopper accounts from erps
        $shoppers = array();        
        foreach($erpAccounts as $erpAccount){
            
            $collection = Mage::getResourceModel('customer/customer_collection')
                ->addFieldToFilter('erpaccount_id', $erpAccount);
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $shoppers = $shoppers + $collection->getItems();
        }
        $shopperIds = array();
        foreach($shoppers as $shopper){
            $shopperIds[] = $shopper->getId();
        }  
        //add current salesrep customer id to list
        $shopperIds[] = Mage::getModel('customer/session')->getId();                
        
        $collection = Mage::getModel('epicor_lists/list')->getCollection()
                ->addFieldToFilter('type','Pr')
                ->addFieldToFilter('owner_id',array('in'=>$shopperIds));
        $salesrepid = Mage::getResourceModel('customer/customer')->getAttribute('sales_rep_id'); 
        $firstname = Mage::getResourceModel('customer/customer')->getAttribute('firstname'); 
        $lastname = Mage::getResourceModel('customer/customer')->getAttribute('lastname'); 
        $email = Mage::getResourceModel('customer/customer')->getAttribute('email'); 
        $erpaccountid = Mage::getResourceModel('customer/customer')->getAttribute('erpaccount_id'); 
        $creatorName = Mage::getResourceModel('customer/customer')->getAttribute('creator_name'); 
        $collection->getSelect()->joinLeft(array('ccfn' =>$firstname->getBackend()->getTable()),"ccfn.entity_id = main_table.owner_id AND ccfn.attribute_id = ".(int) $firstname->getAttributeId(), array('firstname'=>'value')); 
        $collection->getSelect()->joinLeft(array('ccln' =>$lastname->getBackend()->getTable()),"ccln.entity_id = main_table.owner_id AND ccln.attribute_id = ".(int) $lastname->getAttributeId(),  array('lastname'=>'value', 'creator_name' => "CONCAT(ccfn.value, ' ', ccln.value)")); 
        $collection->getSelect()->joinLeft(array('cceai' =>$erpaccountid->getBackend()->getTable()),"cceai.entity_id = main_table.owner_id AND cceai.attribute_id = ".(int) $erpaccountid->getAttributeId(),array('erpaccount_id'=>'value')); 
        $collection->getSelect()->joinLeft(array('cce' =>$email->getBackend()->getTable()),"cce.entity_id = main_table.owner_id", array('email')); 
        
        //Nb to get the sales rep name, we must get the sales_rep value for the erp assigned to the customer and then the salesrep name from the salesrep_account 
        $collection->getSelect()->joinLeft(array('esea' => $collection->getTable('epicor_comm/erp_customer_group')), "esea.entity_id = cceai.value", array('sales_rep'));
        $collection->getSelect()->joinLeft(array('esa' => $collection->getTable('epicor_salesrep/account')), "esa.sales_rep_id = esea.sales_rep", array('name'));

        $collection->getSelect()->columns(array('product_count' => new Zend_Db_Expr('(SELECT count(*) FROM epicor_lists_list_product AS sllp 
                                                    INNER JOIN catalog_product_entity AS cp 
                                                    where sllp.list_id = main_table.id 
                                                    AND sllp.sku = cp.sku
                                                    AND cp.type_id != "grouped")' 
                                                )));
        
        $this->setCollection($collection);        
        return parent::_prepareCollection();
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
        
        $this->addColumn('id', array(
            'header' => $this->__('ID'),
            'index' => 'id',
            //'width'    => '450px',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));
        $this->addColumn('firstname', array(
            'header' => $this->__('firstname'),
            'index' => 'firstname',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));
        $this->addColumn('lastname', array(
            'header' => $this->__('firstname'),
            'index' => 'lastname',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        $this->addColumn('title', array(
            'header' => $this->__('Title'),
            'index' => 'title',
            'type' => 'text',   
            'filter_condition_callback' => array($this,'_titleFilter')
        ));        
        $this->addColumn('creator_name', array(
            'header' => $this->__('Creator Name'),
            'index' => 'creator_name',
            'type' => 'text',
            'filter_condition_callback' => array($this,'_creatorFilter')
        ));        
        $this->addColumn('email', array(
            'header' => $this->__('Email'),
            'index' => 'email',
            'type' => 'text'
        ));        
        $this->addColumn('product_count', array(
            'header' => $this->__('No of Products'),
            'index' => 'product_count',
            'type' => 'text',
            'filter' => false
        ));   
        $this->addColumn('active', array(
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
            'filter_condition_callback' => array($this,'_statusFilter')
        ));
        $this->addColumn('name', array(
            'header' => $this->__('Sales Rep Account Name'),
            'index' => 'name',
            'filterable' => true,
            'type' => 'text',
            'filter_index'=>'name',
           'filter_condition_callback' => array($this,'_salesRepNameFilter')
            
        ));   
        
        $this->addColumn(
            'select', array(
            'header' => $this->__('Actions'),
            'index' => 'id',
            'renderer' => 'epicor_lists/contract_select_grid_renderer_select',  // formats the same, so can use
            'links' => 'true',
            'getter' => 'getId',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id',
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
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
            'confirm' => Mage::helper('epicor_salesrep')->__('Delete selected Lists?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('epicor_salesrep')->__('Change Status'),
            'url' => $this->getUrl('*/*/massAssignStatus'),
            'confirm' => Mage::helper('epicor_salesrep')->__('Change Status of Selected Lists?')
        ));
        
        return $this;
    }
    
    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/pricelistgrid', array(
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
                $collection->getSelect()->where('active = 1');  
                break;
            
            case self::LIST_STATUS_DISABLED:
                $collection->getSelect()->where('active = 0');  
                break;
            
            case self::LIST_STATUS_ENDED:
                $collection->getSelect()->where('active = 1 AND end_date < "'.now().'"');  
                break;
            
            case self::LIST_STATUS_PENDING:
                 $collection->getSelect()->where('active = 1 AND end_date IS NULL OR end_date >= "'.now().'"');         
                
                break;
        }
        
        return $this;
    }
    public function _titleFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }               
        $collection->getSelect()->where('title LIKE "%'.$column->getFilter()->getValue().'%"');    
        return $this;
    }
    public function _creatorFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }          
        $collection->getSelect()->where('CONCAT(ccfn.value, " ", ccln.value) LIKE "%'.$column->getFilter()->getValue().'%"');    
        return $this;
    }
    public function _salesRepNameFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }          
        $collection->getSelect()->where('esa.name LIKE "%'.$column->getFilter()->getValue().'%"');    
        return $this;
    }
          
}