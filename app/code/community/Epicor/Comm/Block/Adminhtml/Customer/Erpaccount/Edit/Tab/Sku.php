<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customersku
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Sku extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('customer_sku_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('product_sku');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
//        $this->setRowInitCallback("var customerSku = new Epicor_CustomerSku.customerSku('customer_sku_form','customers_sku_table'); customerSku.rowInit.bind(customerSku)");
    }
    
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Customer SKU';
    }

    public function getTabTitle() {
        return 'Customer SKU';
    }

    public function isHidden() {
        return false;
    }

    public function getErpCustomer() {
        if (!$this->_erp_customer) {
            $this->_erp_customer = Mage::registry('customer_erp_account');
        }
        return $this->_erp_customer;
    }
    
    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_comm/customer_sku')
                ->getCollection();

        /* @var $collection Epicor_Comm_Model_Mysql4_Erp_Customer_Sku_Collection */
        $collection->getProductSelect();
        $collection->addFieldToFilter('customer_group_id', $this->getErpCustomer()->getId());
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('epicor_comm')->__('Product'),
            //'width' => '150',
            'index' => 'product_sku',
            'filter_index' => 'product_table.sku'
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Sku'),
            //'width' => '150',
            'index' => 'sku',
            'filter_index' => 'main_table.sku'
        ));
        $this->addColumn('description', array(
            'header' => Mage::helper('epicor_comm')->__('Description'),
            'index' => 'description'
        ));
        
          /* CPN EDITING REMOVED UNTIL IS COMPLETE
          $this->addColumn('actions',
            array(
                'header'    =>  Mage::helper('epicor_comm')->__('Actions'),
                'width'     => '100',
//                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Edit'),
                        'onclick'   => 'javascript: customerSku.rowEdit(this)',
                    ),
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Delete'),
                        'onclick'   => 'javascript: if(window.confirm(\''
                               . addslashes($this->escapeHtml(Mage::helper('epicor_comm')->__('Are you sure you want to do this?')))
                               . '\')){customerSku.rowDelete(this);} return false;',
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
                'links' => 'true',
        ));
          
        $this->addColumn('rowdata', array(
            'header' => Mage::helper('flexitheme')->__(''),
            'align' => 'left',
            'width' => '1',
            'name' => 'rowdata',
                'filter'    => false,
                'sortable'  => false,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
            'column_css_class'=> 'no-display last',
            'header_css_class'=> 'no-display last',
        ));
           */

        return parent::_prepareColumns();
    }
    
    public function getGridUrl() {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/skugrid', $params);
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Add'),
                            'onclick' => "customerSku.add();",
                            'class' => 'task'
                        ))
        );
        return parent::_prepareLayout();
    }

    /* CPN EDITING REMOVED UNTIL IS COMPLETE
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
     */

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function getSkipGenerateContent()
    {
        return false;
    }

    public function getTabClass()
    {
        return 'ajax notloaded';
    }

}
