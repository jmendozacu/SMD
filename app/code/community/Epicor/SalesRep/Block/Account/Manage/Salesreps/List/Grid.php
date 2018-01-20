<?php

/**
 * Sales Rep Account Sales Rep List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Salesreps_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrepGrid');
        $this->setSaveParametersInSession(false);

        $this->setIdColumn('entity_id');
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setCacheDisabled(true);
        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
    }

    protected function _prepareCollection()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRep = $helper->getManagedSalesRepAccount();

        $collection = $salesRep->getSalesRepsCollection(Mage::app()->getWebsite()->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('salesrep_id', array(
            'header' => Mage::helper('epicor_salesrep')->__('Sales Rep ID'),
            'width' => '150',
            'index' => 'sales_rep_id',
            'filter_index' => 'sales_rep_id'
        ));

        $this->addColumn('salesrep_name', array(
            'header' => Mage::helper('epicor_salesrep')->__('Customer'),
            'width' => '150',
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('salesrep_email', array(
            'header' => Mage::helper('epicor_salesrep')->__('Email'),
            'width' => '150',
            'index' => 'email',
            'filter_index' => 'email'
        ));

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->addColumn('action', array(
                'header' => Mage::helper('epicor_salesrep')->__('Action'),
                'width' => '100',
                'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
                'links' => 'true',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('epicor_salesrep')->__('Unlink'),
                        'url' => array('base' => '*/*/unlinkSalesRep'),
                        'field' => 'salesreps',
                        'confirm' => Mage::helper('epicor_salesrep')->__('Are you sure you want to unlink this sales rep from the sales rep account?')
                    ),
                    array(
                        'caption' => Mage::helper('epicor_salesrep')->__('Delete'),
                        'url' => array('base' => '*/*/deleteSalesRep'),
                        'field' => 'salesreps',
                        'confirm' => Mage::helper('epicor_salesrep')->__('Are you sure you want to delete this sales rep? This action cannot be undone')
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->setMassactionIdField('id');
            $this->getMassactionBlock()->setFormFieldName('salesreps');

            $this->getMassactionBlock()->addItem('unlink', array(
                'label' => Mage::helper('epicor_salesrep')->__('Unlink'),
                'url' => $this->getUrl('*/*/unlinkSalesRep'),
                'confirm' => Mage::helper('epicor_salesrep')->__('Unlink selected Sales Reps?')
            ));

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('epicor_salesrep')->__('Delete'),
                'url' => $this->getUrl('*/*/deleteSalesRep'),
                'confirm' => Mage::helper('epicor_salesrep')->__('Delete selected Sales Reps? This action cannot be undone')
            ));
        }

        return $this;
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label' => Mage::helper('adminhtml')->__('Add'),
                                'onclick' => "javascript:\$('sales_rep_add_form').show()",
                                'class' => 'task'
                            ))
            );
        }
        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getMainButtonsHtml()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $html = $this->getAddButtonHtml();
            $html .= parent::getMainButtonsHtml();
            return $html;
        } else {
            return parent::getMainButtonsHtml();
        }
    }

    public function getRowUrl($row)
    {
        return false;
    }

}
