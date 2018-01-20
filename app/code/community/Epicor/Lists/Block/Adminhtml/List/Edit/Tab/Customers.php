<?php

/**
 * List Customers Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Customers extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('customersGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_customers' => 1));
        $jsObjectName = $this->getJsObjectName();
        $this->setAdditionalJavaScript("
            $jsObjectName.eccReload = $jsObjectName.reload;
            $jsObjectName.reload = function(url) {
                if ($('erp_account_link_type') && typeof erpaccountsGridJsObject !== 'undefined') {
                    $('customersGrid_table').style.display='none';
                    this.reloadParams.erp_account_link_type = $('erp_account_link_type').value;
                    this.reloadParams.erp_accounts_exclusion = $('erp_accounts_exclusion').checked ? 'Y' : 'N';
                    this.reloadParams['erp_accounts[]'] = erpaccountsGridJsObject.reloadParams['erpaccounts[]'];
                }
                this.eccReload(url);
            }
            
            Event.observe('form_tabs_customers', 'click', function (event) {
                var el = Event.element(event);
                el = el.up('a');
                
                if ($('erp_account_link_type')) {
                    var link_type = $('erp_account_link_type').value;
                    var exclusion = $('erp_accounts_exclusion').checked ? 'Y' : 'N';
                    var erp_accounts = erpaccountsGridJsObject.reloadParams['erpaccounts[]'];
                    if (
                        erpAccountsReloadParams.erp_account_link_type != link_type ||
                        erpAccountsReloadParams.erp_accounts != erp_accounts ||
                        erpAccountsReloadParams.erp_accounts_exclusion != exclusion
                    ) {
                        customersGridJsObject.reload();
                    }
                    erpAccountsReloadParams.erp_account_link_type = link_type;
                    erpAccountsReloadParams.erp_accounts_exclusion = exclusion;
                    erpAccountsReloadParams['erp_accounts[]'] = erp_accounts;
                }
            });
        ");
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag

        if ($column->getId() == 'selected_customers') {
            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array(0);
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $ids));
            } else if ($ids) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $ids));
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
        return 'Customers';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Customers';
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
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->list;
    }

    /**
     * Build data for List Customers
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Customers
     */
    protected function _prepareCollection()
    {
        $listId = $this->getRequest()->getParam('id');
        
        $erpLinkType = $this->getRequest()->getParam('erp_account_link_type');
        $exclusion = $this->getRequest()->getParam('erp_accounts_exclusion');
        $erpAccountIds = $this->getRequest()->getParam('erp_accounts');
        
        if (!$erpLinkType) {
            $erpAccountIds = $this->_getSeletedErpId($listId);
            $erpLinkType = $this->getList()->getErpAccountLinkType();
            $exclusion = $this->getList()->getErpAccountsExclusion();
        }

        $allowedCustomerTypes = array('salesrep', 'customer');
        $typeNull = false;
        $types = false;
        if (($erpLinkType == "N")) {
            // When the link type of the list is “No specific link” then the customers tab should show “guests”
            array_push($allowedCustomerTypes, 'guest');
        } else if ($erpLinkType == "B") {
            $types = array('B2B');
        } else if ($erpLinkType == "C") {
            $types = array('B2C');
            if ($exclusion == 'Y' && empty($erpAccountIds)) {
                array_push($allowedCustomerTypes, 'guest');
                $typeNull = true;
            }
        }
        
        $erpAccountIds = $this->getValidErpAccountIds($erpAccountIds, $erpLinkType, $exclusion);
        
        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $erpaccountTable = $collection->getTable('epicor_comm/customer_erpaccount');
        $salesRepTable = $collection->getTable('epicor_salesrep/account');
        $collection->addNameToSelect();
        $collection->addAttributeToSelect('email');
        $collection->addAttributeToSelect('erpaccount_id', 'left');
        $collection->addAttributeToSelect('ecc_erp_account_type', 'left');
        $collection->addAttributeToSelect('sales_rep_account_id', 'left');
        $collection->addAttributeToSelect('ecc_master_shopper');
        $collection->joinTable(array('cc' => $erpaccountTable), 'entity_id=erpaccount_id', array('customer_erp_code' => 'erp_code', 'customer_company' => 'company', 'customer_short_code' => 'short_code', 'account_type' => 'account_type'), null, 'left');
        $collection->joinTable(array('sr' => $salesRepTable), 'id=sales_rep_account_id', array('sales_rep_id' => 'sales_rep_id'), null, 'left');
        $collection->addExpressionAttributeToSelect('joined_short_code', "IF(sr.sales_rep_id IS NOT NULL, sr.sales_rep_id, IF(cc.short_code IS NOT NULL, cc.short_code, IF(cc.short_code IS NOT NULL, cc.short_code, '')))", 'erpaccount_id');
        $collection->addAttributeToFilter('ecc_erp_account_type', array('in' => $allowedCustomerTypes));

        if ($erpLinkType != 'N' && $typeNull == false) {
            if (empty($erpAccountIds)) {
                $erpAccountIds = array(0);
            }
            
            $filters = array(
                array('attribute' => 'erpaccount_id', array('in' => $erpAccountIds)),
                array('attribute' => 'sales_rep_account_id', 'neq' => '0')
            );
            
            $collection->addFieldToFilter($filters);
        }
        
        // If ERP Link Type is B2B it should only list B2B customers and sales reps. (Similarly if Link Type is B2C - it should only list B2C customers and sales reps) 
        if ($types) {
            $filters = array(
                array('attribute' => 'account_type', $types),
                array('attribute' => 'sales_rep_account_id', 'neq' => '0')
            );

            if ($typeNull) {
                $filters[] = array('attribute' => 'account_type', array('null' => true));
            }
            $collection->addFieldToFilter($filters);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Gets an array of valid erp account ids based on the flags of the list and the ids passed
     * 
     * @param array $erpAccountIds
     * @param string $erpLinkType
     * @param string $exclusion
     * @return array
     */
    protected function getValidErpAccountIds($erpAccountIds, $erpLinkType, $exclusion)
    {
        if ($erpLinkType == 'N') {
            return array();
        }
        
        $erpAccountsCollection = Mage::getModel('epicor_comm/customer_erpaccount')->getCollection();
        /* @var $erpAccountsCollection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Collection */
        if (in_array($erpLinkType, array('B', 'C'))) {
            $erpAccountsCollection->addFieldToFilter('account_type', $erpLinkType == 'B' ? 'B2B' : 'B2C');
        }
        
        $condition = $exclusion == 'Y' ? 'nin' : 'in';
        $erpAccountIdFilter = empty($erpAccountIds) ? array(0) : $erpAccountIds;
        $erpAccountsCollection->addFieldToFilter('entity_id', array($condition => $erpAccountIdFilter));

        return $erpAccountsCollection->getAllIds();
    }

    /**
     * Build columns for List Customers
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Customers
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn('selected_customers', array(
            'header' => $helper->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'selected_customers',
            'values' => $this->_getSelected(),
            'align' => 'center',
            'index' => 'entity_id',
            'filter_index' => 'main_table.entity_id',
            'sortable' => false,
            'field_name' => 'links[]'
        ));

        $this->addColumnAfter('customer_short_code', array(
            'header' => Mage::helper('epicor_comm')->__('Short Code'),
            'index' => 'customer_short_code',
            'width' => '90',
            ), 'customer_company');



        $this->addColumn('ecc_erp_account_type', array(
            'header' => Mage::helper('epicor_comm')->__('Customer Type'),
            'index' => 'erp_account_type',
            'filter_index' => 'ecc_erp_account_type',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Customer_Grid_Renderer_Accounttype(),
            'type' => 'options',
            'width' => '100px',
            'options' => Mage::helper('epicor_common/account_selector')->getAccountTypeNames('supplier'),
        ));

        $this->addColumn('account_type', array(
            'header' => Mage::helper('epicor_comm')->__('ERP Account Type'),
            'index' => 'account_type',
            'filter_index' => 'account_type',
            'type' => 'options',
            'width' => '100px',
            'options' => array(
                'B2B' => 'B2B',
                'B2C' => 'B2C',
                '' => 'N/A'
            )
        ));

        $this->addColumn(
            'customer_name', array(
            'header' => $helper->__('Customer'),
            'index' => 'name',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'email', array(
            'header' => $helper->__('Email'),
            'index' => 'email',
            'type' => 'text'
            )
        );

        $this->addColumn('ecc_master_shopper', array(
            'header' => $helper->__('Master Shopper'),
            'width' => '50',
            'index' => 'ecc_master_shopper',
            'align' => 'center',
            'type' => 'options',
            'options' => array('1' => 'Yes', '0' => 'No')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header' => Mage::helper('customer')->__('Website'),
                'align' => 'center',
                'width' => '150',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ));
        }

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
     * Used in grid to return selected Customers values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected Customers
     * 
     * @return array
     */
    public function getSelected()
    {
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $list = $this->getList();
            /* @var $list Epicor_Lists_Model_List */

            foreach ($list->getCustomers() as $customer) {
                $this->_selected[$customer->getId()] = array('id' => $customer->getId());
            }
        }
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
        );
        return $this->getUrl('adminhtml/epicorlists_list/customersgrid', $params);
    }

    /**
     * Row Click URL
     *
     * @param Epicor_Comm_Model_Customer $row
     * 
     * @return null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    protected function _getSeletedErpId($listId = false)
    {
        $listsCollection = Mage::getModel('epicor_lists/list_erp_account')->getCollection();
        /* @var $listsCollection Epicor_Lists_Model_Resource_List_Erp_Account_Collection */
        $listsCollection->addFieldtoFilter('list_id', array('in' => $listId));
        $erpAccountId = array('0' => 0);
        if ($listsCollection->count()) {
            $i = 1;
            foreach ($listsCollection->getData() as $lists) {
                $erpAccountId[$i] = $lists['erp_account_id'];
                $i++;
            }
            return $erpAccountId;
        } else {
            return $erpAccountId;
        }
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
                    'onclick' => "javascript:customerSelectAll()",
                    'class' => 'task'
                ))
        );

        $this->setChild(
            'unselect_all', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Unselect All'),
                    'onclick' => "javascript:customerUnselectAll()",
                    'class' => 'task'
                ))
        );

        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getSelectButtonHtml();
        $html .= $this->getUnselectButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function getEmptyText()
    {
        $type = Mage::getModel('epicor_lists/list_type')->getListLabel($this->getList()->getType());
        return $this->__('No Customers Selected. %s not restricted by Customer', $type);
    }

}
