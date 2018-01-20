<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('entity_id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('accounts');

        $groups = $this->helper('customer')->getGroups()->toOptionArray();
        $this->getMassactionBlock()->addItem('cusgroup', array(
            'label' => Mage::helper('epicor_comm')->__('Assign Customer Group'),
            'url' => $this->getUrl('*/*/massGroupassign'),
            'confirm' => Mage::helper('epicor_comm')->__('Are you sure?'),
            'additional' => array(
                'visibility' => array(
                    'name' => 'customerGroup',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('customer')->__('Customer Groups'),
                    'values' => $groups
                )
            )
        ));
        
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('epicor_comm')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('epicor_comm')->__('Delete selected ERP Accounts?')
        ));
        
        /* CPN EDITING REMOVED UNTIL IS COMPLETE
        $this->getMassactionBlock()->addItem('cpnediting', array(
            'label' => Mage::helper('epicor_comm')->__('Change CPN Editing'),
            'url' => $this->getUrl('<CHANGETHIS>/massCpnEditing'),
            'confirm' => Mage::helper('epicor_comm')->__('Are you sure?'),
            'additional' => array(
                'visibility' => array(
                    'name' => 'cpnEditing',
                    'type' => 'select',
                    'label' => Mage::helper('customer')->__('CPN Editing'),
                    'values' => Mage::getModel('epicor_comm/config_source_yesnonulloption')->toOptionArray()
                )
            )
        ));
        */
        
        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_SalesRep')) {
            $this->getMassactionBlock()->addItem('assign_sales_rep_account', array(
                'label' => Mage::helper('customer')->__('Assign a Sales Rep Account'),
                'url' => $this->getUrl('adminhtml/epicorsalesrep_customer_salesrep/massAssignToErpAccounts'),
                'additional' => array(
                    'sales_rep_account' => array(
                        'name' => 'sales_rep_account',
                        'type' => 'sales_rep_account',
                        'renderer' => array(
                            'type' => 'sales_rep_account',
                            'class' => 'Epicor_SalesRep_Block_Adminhtml_Form_Element_Salesrepaccount'
                        ),
                        'label' => Mage::helper('customer')->__('Sales Rep Account'),
                        'required' => true
                        //'values' => $accounts
                    )
                )
            ));
        }

        return $this;
    }

    protected function _prepareColumns() {
        parent::_prepareColumns();

        
        $this->addColumn('company', array(
            'header' => Mage::helper('epicor_comm')->__('Company'),
            'index' => 'company',
            'width' => '20px',
            'filter_index' => 'company'
        ));
        
        $this->addColumn('short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'short_code',
            'width' => '20px',
            'filter_index' => 'short_code'
        ));
        
        $this->addColumn('account_number', array(
            'header' => Mage::helper('epicor_comm')->__('ERP Account Number'),
            'index' => 'account_number',
            'width' => '20px',
            'filter_index' => 'account_number'
        ));

        if (Mage::helper('epicor_comm')->isModuleEnabled('Epicor_Supplierconnect')) {
            $this->addColumn('account_type', array(
                'header' => Mage::helper('epicor_comm')->__('ERP Account Type'),
                'index' => 'account_type',
                'width' => '20px',
                'filter_index' => 'account_type',
            ));
        }

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name',
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

        $this->addColumn('customer_group_code', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Group'),
            'width' => '100',
            'index' => 'magento_id',
            'type' => 'options',
            'options' => $groups,
        ));

        $this->addColumn('onstop', array(
            'header' => Mage::helper('epicor_comm')->__('On Stop'),
            'index' => 'onstop',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('epicor_comm')->__('Yes'),
                '0' => Mage::helper('epicor_comm')->__('No'),
            ),
            'filter_index' => 'onstop',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('epicor_comm')->__('Created'),
            'index' => 'created_at',
            'width' => '200px',
            'filter_index' => 'created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('epicor_comm')->__('Last ERP Update'),
            'index' => 'updated_at',
            'width' => '200px',
            'type' => 'datetime',
            'filter_index' => 'updated_at',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));

        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
