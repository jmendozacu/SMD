<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_Pricingrules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('pricing_rules');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setRowInitCallback('pricingRules.rowInit.bind(pricingRules)');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('desc');
    }

    /**
     * 
     * @return Epicor_SalesRep_Model_Account
     */
    public function getSalesRepAccount()
    {
        if (!$this->_salesrep) {
            $this->_salesrep = Mage::registry('salesrep_account');
        }

        return $this->_salesrep;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_salesrep/pricing_rule')->getCollection();
        /* @var $collection Epicor_SalesRep_Model_Resource_Pricing_Rule_Collection */
        $salesRepAccount = $this->getSalesRepAccount();
        $collection->addFieldToFilter('sales_rep_account_id', $salesRepAccount->getId());
        $this->setCollection($collection);
//        echo '<pre>';
//        foreach($collection->getItems() as $item) {
//            var_dump($item->getData());
//        }
//        echo '</pre>';
        return parent::_prepareCollection();
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Add'),
                            'onclick' => 'pricingRules.add()',
                            'class' => 'task'
                        ))
        );
        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id', array(
            'header' => Mage::helper('epicor_salesrep')->__('ID'),
            'width' => '50',
            'index' => 'id',
            'filter_index' => 'id'
        ));

        $this->addColumn('rule_name', array(
            'header' => Mage::helper('epicor_salesrep')->__('Name'),
            'index' => 'name',
            'filter_index' => 'name'
        ));

        $this->addColumn('from_date', array(
            'type' => 'date',
            'header' => Mage::helper('epicor_salesrep')->__('Date Start'),
            'index' => 'from_date',
            'filter_index' => 'from_date'
        ));

        $this->addColumn('to_date', array(
            'type' => 'date',
            'header' => Mage::helper('epicor_salesrep')->__('Date Expire'),
            'index' => 'to_date',
            'filter_index' => 'to_date'
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('epicor_salesrep')->__('Status'),
            'index' => 'is_active',
            'filter_index' => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('catalogrule')->__('Active'),
                0 => Mage::helper('catalogrule')->__('Inactive')
            ),
        ));

        $this->addColumn('priority', array(
            'type' => 'number',
            'header' => Mage::helper('epicor_salesrep')->__('Priority Order'),
            'index' => 'priority',
            'filter_index' => 'priority'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_salesrep')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => 'adminhtml/epicorsalesrep_customer_salesrep/deletepricingrule'),
                    'field' => 'id',
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
        ));

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

    protected function _getSelected()
    {   // Used in grid to return selected customers values.
        return array_keys($this->getSelected());
    }

    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $collection = Mage::getResourceModel('customer/customer_collection');

            if ($this->getSalesRepAccount()->getId()) {
                $collection->addFieldToFilter('salesrep_id', $this->getSalesRepAccount()->getId());
            }

            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            foreach ($collection->getAllIds() as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
        return $this->_selected;
    }

    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getSalesRepAccount()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorsalesrep_customer_salesrep/pricingrulesgrid', $params);
    }

    public function getRowUrl($row)
    {
        return "javascript:pricingRules.rowEdit(this, " . $row->getId() . ");";
    }

    protected function _toHtml()
    {
        if(!$this->getSalesRepAccount() || !$this->getSalesRepAccount()->getId()) {
            return '<div id="messages"><ul class="messages"><li class="warning-msg"><ul><li><span>' . $this->__('Pricing rules can not be created until you have saved the sales rep for the first time') . '</span></li></ul></li></ul></div>';
        } else {
            return parent::_toHtml();
        }
    }

}
