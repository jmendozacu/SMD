<?php

/**
 * Sales Rep Account Pricing Rules List
 * 
 * @category   Epicor
 * @package    Epicor_SalesRep
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Pricingrules_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('pricing_rules');
        $this->setSaveParametersInSession(false);
        $this->setRowInitCallback('pricingRules.rowInit.bind(pricingRules)');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_salesrep/pricing_rule')->getCollection();
        /* @var $collection Epicor_SalesRep_Model_Resource_Pricing_Rule_Collection */

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        $salesRepAccount = $helper->getManagedSalesRepAccount();

        $collection->addFieldToFilter('sales_rep_account_id', $salesRepAccount->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('rule_name', array(
            'header' => Mage::helper('epicor_salesrep')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('from_date', array(
            'type' => 'date',
            'header' => Mage::helper('epicor_salesrep')->__('Start Date'),
            'index' => 'from_date',
            'filter_index' => 'from_date'
        ));

        $this->addColumn('to_date', array(
            'type' => 'date',
            'header' => Mage::helper('epicor_salesrep')->__('Expiry Date'),
            'index' => 'to_date',
            'filter_index' => 'to_date'
        ));

        $this->addColumn('is_active', array(
            'type' => 'options',
            'header' => Mage::helper('epicor_salesrep')->__('Status'),
            'index' => 'is_active',
            'filter_index' => 'is_active',
            'options' => array(
                '1' => 'Active',
                '0' => 'Inactive'
            ),
        ));

        $this->addColumn('priority', array(
            'type' => 'number',
            'header' => Mage::helper('epicor_salesrep')->__('Priority Order'),
            'index' => 'priority',
            'filter_index' => 'priority'
        ));

        $this->addColumn('action_operator', array(
            'type' => 'options',
            'header' => Mage::helper('epicor_salesrep')->__('Action Price Base'),
            'index' => 'action_operator',
            'filter_index' => 'action_operator',
            'options' => array(
                'cost' => '% above Cost Price',
                'list' => '% below Customer Price',
                'base' => '% below Base Price',
            ),
        ));

        $this->addColumn('action_amount', array(
            'type' => 'number',
            'header' => Mage::helper('epicor_salesrep')->__('Margin %'),
            'index' => 'action_amount',
            'filter_index' => 'action_amount'
        ));

        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->addColumn('action', array(
                'header' => Mage::helper('epicor_salesrep')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('epicor_comm')->__('Delete'),
                        'url' => array('base' => '*/*/deletepricingrule'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'is_system' => true,
            ));
        }
        
        $this->addColumn('conditions', array(
            'header' => Mage::helper('flexitheme')->__('Conditions'),
            'name' => 'conditions',
            'renderer' => 'epicor_salesrep/adminhtml_widget_grid_column_renderer_conditions',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));

        $this->addColumn('rowdata', array(
            'header' => Mage::helper('flexitheme')->__('Row'),
            'align' => 'left',
            'name' => 'rowdata',
            'width' => 0,
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_rowdata',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */

        if ($helper->canEdit()) {
            $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label' => Mage::helper('adminhtml')->__('Add'),
                                'onclick' => 'pricingRules.add()',
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
        $helper = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        
        return "javascript:pricingRules.rowEdit(this, " . $row->getId() . ");";
    }

}
