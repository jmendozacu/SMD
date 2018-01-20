<?php

/**
 * Branchpickup select page grid
 *
 * @category   Epicor
 * @package    Epicor_Branchpickup
 * @author     Epicor Websales Team
 */
class Epicor_BranchPickup_Block_Pickup_Select_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    private $_selected = array();
    private $_erpAccount;
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('selectgrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->redirectSalesRep();
        $this->checkActive();
    }
    
    
    protected function _prepareLayout()
    {
        
        $urlRedirect = $this->getUrl('*/*/removebranchpickup', array(
            '_current' => true,
            'contract' => $this->getRequest()->getParam('contract')
        ));
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('adminhtml')->__('Remove Selected Branch Pickup'),
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

    /**
     * Redirect User If the Masquerade account was not selected
    */
    public function redirectSalesRep()
    {
        $helper = Mage::helper('epicor_branchpickup');
        /* @var Epicor_BranchPickup_Helper_Data */
        $isSalesRepSelected = $helper->salesRepRedirect();
        if($isSalesRepSelected) {
            Mage::getSingleton('core/session')->addNotice('Please select a Masquerade account');
            Mage::app()->getFrontController()->getResponse()->setRedirect($isSalesRepSelected);            
        }
    }
    
    
    public function checkActive()
    {
        $helper = Mage::helper('epicor_branchpickup');
        /* @var Epicor_BranchPickup_Helper_Data */
        $branchPickupActive = $helper->branchPickupActive();
        if(!$branchPickupActive) {
            Mage::getSingleton('core/session')->addNotice('You are not authorized to access this page');
            Mage::app()->getFrontController()->getResponse()->setRedirect('/');            
        }
    }    
    
    /**
     * Build data for List Locations
     */
    protected function _prepareCollection()
    {
        $locationIds = $this->_getSelected();
        $collection  = Mage::getModel('epicor_comm/location')->getCollection();
        $collection->addFieldToFilter('code', array(
            'in' => $locationIds
        ));
        $collection->getSelect()->order('sort_order ASC');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * Used in grid to return selected customers values.
     */    
    
    protected function _getSelected()
    {
        $helperbranch = Mage::helper('epicor_branchpickup');
        /* @var Epicor_BranchPickup_Helper_Data */
        return array_keys($helperbranch->getSelected());
    }
    
    
    /**
     * Configuration of grid
     * @return Mage_Adminhtml_Block_Widget_Grid
     * Build columns for List Addresses
     */
    protected function _prepareColumns()
    {
        

        
        $this->addColumn('location_name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'width' => '150',
            'index' => 'name',
            'filter_index' => 'name'
        ));
        
        $this->addColumn('address1', array(
            'header' => Mage::helper('epicor_comm')->__('Street'),
            'width' => '150',
            'index' => 'address1',
            'filter_index' => 'address1',
            'renderer' => new Epicor_BranchPickup_Block_Pickupsearch_Select_Renderer_Street(),
            'filter' => false,
            'sortable' => false,               
        ));
        
        $this->addColumn('city', array(
            'header' => Mage::helper('epicor_comm')->__('City'),
            'width' => '150',
            'index' => 'city',
            'filter_index' => 'city'
        ));
        
        $this->addColumn('county', array(
            'header' => Mage::helper('epicor_comm')->__('Region'),
            'width' => '150',
            'index' => 'county',
            'filter_index' => 'county'
        ));
        
        
        $this->addColumn('country', array(
            'header' => Mage::helper('epicor_comm')->__('Country'),
            'width' => '150',
            'index' => 'country',
            'type' =>'country',
            'filter_index' => 'country'
        ));
        
        $this->addColumn('postcode', array(
            'header' => Mage::helper('epicor_comm')->__('Postal Code'),
            'width' => '150',
            'index' => 'postcode',
            'filter_index' => 'postcode'
        ));
        
        $this->addColumn('select', array(
            'header' => $this->__('Select'),
            'width' => '280',
            'index' => 'code',
            'renderer' => 'epicor_branchpickup/pickup_select_grid_renderer_select',
            'links' => 'true',
            'getter' => 'getCode',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Select'),
                    'url' => '',
                    'id' => 'link',
                    'onclick' => 'changeBranPickupLocation(this); return false;'
                )
            )
        ));
        
        
        $this->addColumn('location_code', array(
            'header' => Mage::helper('epicor_comm')->__('Location Code'),
            'width' => '0',
            'index' => 'code',
            'filter_index' => 'code',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));        
        
        $this->addColumn('row_id', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'code',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));
        
        
        return parent::_prepareColumns();
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/selectgrid', array(
            '_current' => true
        ));
    }
}