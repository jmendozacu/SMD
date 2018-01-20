<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Hierarchy_Parents_Grid extends Epicor_Common_Block_Generic_List_Grid {

    protected $_defaultLimit = 10000;
    
    public function __construct()
    {
        parent::__construct();

        $this->setId('parents');
        $this->setDefaultSort('type');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('epicor_common');
        $this->setIdColumn('id');

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setCacheDisabled(true);
        $this->setShowAll(true);
        $this->setUseAjax(false);
        $this->setSkipGenerateContent(true);

        $this->setCustomData($this->getCustomData());
    }

    protected function getCustomData()
    {
        $erpAccount = Mage::registry('customer_erp_account');
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        
        return $erpAccount->getParents();
    }

    protected function _getColumns()
    {
        $columns = array();

        $columns['parent_type'] = array(
            'header' => Mage::helper('epicor_comm')->__('Type'),
            'align' => 'left',
            'index' => 'parent_type',
            'sortable' => false,
            'renderer' => new Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Hierarchy_Renderer_Type()
        );

        $columns['account_number'] = array(
            'header' => Mage::helper('epicor_comm')->__('Account Code'),
            'align' => 'left',
            'index' => 'account_number',
            'sortable' => false
        );
        
        $columns['name'] = array(
            'header' => Mage::helper('epicor_comm')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'sortable' => false
        );

        $columns['deleted'] = array(
            'header' => Mage::helper('epicor_comm')->__('Delete'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'deleted_parents[]',
            'name' => 'deleted_parents',
            'values' => array(),
            'align' => 'center',
            'editable' => true,
            'index' => 'parent_type',
            'sortable' => false
        );


        return $columns;
    }

    public function getRowUrl($row)
    {
        return null;
    }

    public function getEmptyText()
    {
        return $this->__('No Parent Accounts');
    }

}
