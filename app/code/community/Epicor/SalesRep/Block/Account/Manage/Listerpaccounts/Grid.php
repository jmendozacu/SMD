<?php

/**
 * List ERP Accounts Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Account_Manage_Listerpaccounts_Grid extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('listerpaccountsGrid');
 //       $this->setUseAjax(false);
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_erpaccounts' => 1));
        $this->setIdColumn('id');
        $this->setCacheDisabled(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_erpaccounts') {
            $ids = $this->_getSelected();

            if (!empty($ids)) {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('in' => $ids));
                } else {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('nin' => $ids));
                }
            } else {
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('in' => array('')));
                } else {
                    $this->getCollection()->addFieldToFilter('main_table.entity_id', array('nin' => array('')));
                }
            }
            
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Is this tab shown?
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab Label
     *
     * @return boolean
     */
    public function getTabLabel()
    {
        return 'ERP Accounts';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'ERP Accounts';
    }

    /**
     * Is this tab hidden?
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('list_id'));
            }
        }
        return $this->list;
    }

    /**
     * Build data for List ERP Accounts
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts
     */
     protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */

        $helper      = Mage::helper('epicor_salesrep/account_manage');
        /* @var $helper Epicor_SalesRep_Helper_Account_Manage */
        $salesRep    = $helper->getBaseSalesRepAccount();
        $erpAccounts = $salesRep->getMasqueradeAccountIds();
        $collection->addFieldToFilter('main_table.entity_id', array('in' => $erpAccounts));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List ERP Accounts
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $isEditable = $this->getList()->getTypeInstance()->isSectionEditable('erpaccounts');
        $this->addColumn('selected_erpaccounts', array(
            'header' => $helper->__('Select'),
            'column_css_class' => $isEditable ? '' : 'no-display',
            'header_css_class' => $isEditable ? 'a-center' : 'no-display',
            'type' => 'checkbox',
            'name' => 'selected_erpaccounts',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'filter_index' => 'main_table.entity_id',
            'sortable' => false,
            'field_name' => 'links[]'
        ));


        $this->addColumn(
                'account_number', array(
            'header' => $helper->__('ERP Account Number'),
            'index' => 'account_number',
            'type' => 'text'
                )
        );

        $this->addColumn('short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'short_code',
            'filter_index' => 'short_code'
        ));


        $this->addColumn(
                'name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
            'type' => 'text'
                )
        );


        $this->addColumn('row_id', array(
            'header' => $helper->__('Position'),
            'name' => 'row_id',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'width' => 0,
            'editable' => true,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Used in grid to return selected ERP Accounts values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected ERP Accounts
     * 
     * @return array
     */
    public function getSelected()
    {
//        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getErpAccounts() as $erpAccount) {
                $this->_selected[$erpAccount->getId()] = array('id' => $erpAccount->getId());
            }
 //       }
        return $this->_selected;
    }

    /**
     * Sets the selected items array
     *
     * @param array $selected
     *
     * @return void
     */
    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $id) {
                $this->_selected[$id] = array('id' => $id);
            }
        }
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getList()->getId(),
            '_current' => true,
        );
        return $this->getUrl('epicor_salesrep/account_manage/listerpaccountsgrid', array('_current'=>true));
    }

    /**
     * Row Click URL
     *
     * @param Epicor_Comm_Model_Customer_Erpaccount $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    public function getSelectButtonHtml()
    {
        return $this->getChildHtml('select_all');
    }

    public function getUnselectButtonHtml()
    {
        return $this->getChildHtml('unselect_all');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
                'select_all', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Select All'),
                            'onclick' => "javascript:listerpaccountsSelectAll()",
                            'class' => 'task'
                        ))
        );

        $this->setChild(
                'unselect_all', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Unselect All'),
                            'onclick' => "javascript:listerpaccountsUnselectAll()",
                            'class' => 'task'
                        ))
        );

        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $isEditable = $this->getList()->getTypeInstance()->isSectionEditable('erpaccounts');
        $html = '';
        if ($isEditable) {
            $html .= $this->getSelectButtonHtml();
            $html .= $this->getUnselectButtonHtml();
        }
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function getEmptyText()
    {
        return $this->__('No ERP Accounts Selected');
    }
//     protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('selected_erpaccounts'); 
//        $this->getMassactionBlock()->setFormFieldName('selected_erpaccounts');
//        parent::_prepareMassaction();
//        return $this;
//    }    

}
