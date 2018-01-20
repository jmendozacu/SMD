<?php

/**
 * Sales Rep Account Hierarchy Children List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Children_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('sales_rep_account_children');

        $this->setIdColumn('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setMessageBase('epicor_salesrep');
        $this->setCustomColumns($this->_getColumns());
        $this->setKeepRowObjectType(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setShowAll(true);

        $children = array();

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRep = $helper->getManagedSalesRepAccount();

        if ($salesRep) {
            $children = $salesRep->getChildAccounts();
        }

        $this->setCustomData($children);
    }

    protected function _getColumns()
    {
        $columns = array();

        $columns['child_sales_rep_id'] = array(
            'header' => Mage::helper('core')->__('Sales Rep Account Number'),
            'align' => 'left',
            'width' => '100',
            'index' => 'sales_rep_id',
            'filter_index' => 'sales_rep_id',
        );

        $columns['child_name'] = array(
            'header' => Mage::helper('core')->__('Name'),
            'align' => 'left',
            'index' => 'name',
            'filter_index' => 'name',
        );

        $columns['action'] = array(
            'header' => Mage::helper('epicor_salesrep')->__('Action'),
            'width' => '100',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getEncodedId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_salesrep')->__('Manage'),
                    'url' => array('base' => '*/*/manage'),
                    'field' => 'salesrepaccount'
                ),
                array(
                    'caption' => Mage::helper('epicor_salesrep')->__('Unlink'),
                    'url' => array('base' => '*/*/unlinkchildaccount'),
                    'confirm' => $this->__('Are you sure you want to unlink this child account from the sales rep account?'),
                    'field' => 'salesrepaccount'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        );

        return $columns;
    }

    /**
     * 
     * @param Epicor_SalesRep_Model_Account $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        return Mage::getUrl('*/*/manage', array('salesrepaccount' => $helper->encodeId($row->getId())));
    }

}
