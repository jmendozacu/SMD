<?php

/**
 * Erp account Allowed Shipstatus grid
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Shipstatus extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    private $_selected = array();

    public function __construct() {
        parent::__construct();
        $this->setId('erpaccount_shipstatus');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('code' => 1));
        $this->setSaveParametersInSession(true);
    }

    public function getErpCustomer() {
        if (!$this->_erp_customer) {
            if (Mage::registry('customer_erp_account')) {
                $this->_erp_customer = Mage::registry('customer_erp_account');
            } else {
                $this->_erp_customer = Mage::getModel('epicor_comm/customer_erpaccount')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->_erp_customer;
    }

    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Shipstatus';
    }

    public function getTabTitle() {
        return 'Shipstatus';
    }

    public function isHidden() {
        return false;
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('customerconnect/erp_mapping_shipstatus')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'code') {

            $ids = $this->_getSelected();
            if (empty($ids)) {
                $ids = array();
            }
            $ids2 = Mage::getModel('customerconnect/erp_mapping_shipstatus')->getDefaultErpshipstatus();
            $newIds = array_merge($ids, $ids2);
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('code', array('in' => $newIds));
            } else {
                if ($newIds) {
                    $this->getCollection()->addFieldToFilter('code', array('nin' => $newIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getSelected() {   // Used in grid to return selected values.
        return array_keys($this->getSelected());
    }

    public function getSelected() {
        $allowed = array();
        $excluded = array();
        if (empty($this->_selected) && $this->getRequest()->getParam('ajax') !== 'true') {
            $erpAccount = $this->getErpCustomer();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

            $allowed = unserialize($erpAccount->getAllowedShipstatusMethods());
            $excluded = unserialize($erpAccount->getAllowedShipstatusMethodsExclude());

            if (!(empty($allowed) && empty($excluded))) {

                $include = !empty($allowed) ? 'Y' : 'N';
                if ($include == 'Y') {
                    foreach ($allowed as $deliveryCode) {
                        $this->_selected[$deliveryCode] = array('code' => $deliveryCode);
                    }
                } else {
                    foreach ($excluded as $deliveryCode) {
                        $this->_selected[$deliveryCode] = array('code' => $deliveryCode);
                    }
                }
            }
        }

        return $this->_selected;
    }

    public function setSelected($selected) {
        if (!empty($selected)) {
            foreach ($selected as $code) {
                $this->_selected[$code] = array('code' => $code);
            }
        }
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header' => Mage::helper('epicor_lists')->__('Select'),
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'code',
            'align' => 'center',
            'index' => 'code',
            'sortable' => false,
            'width' => '20px',
            //'field_name' => 'links[]',
            //'values' => $this->_getSelected(),
            'use_index' => true,
            'renderer' => 'customerconnect/adminhtml_mapping_shipstatus_renderer_checkboxerpgrid',
        ));
        $this->addColumn('erpcode', array(
            'header' => Mage::helper('epicor_comm')->__('Ship Status Code'),
            'align' => 'left',
            'index' => 'code',
        ));

        $this->addColumn('description', array(
            'header' => Mage::helper('epicor_comm')->__('Ship Status Code Description'),
            'align' => 'left',
            'index' => 'description',
        ));
        $this->addColumn('is_default', array(
            'header' => Mage::helper('customerconnect')->__('Default'),
            'width' => '20px',
            'type' => 'checkbox',
            'align' => 'center',
            'index' => 'is_default',
            //'values'   => array(1),
            'filter' => false,
            'renderer' => 'customerconnect/adminhtml_mapping_shipstatus_renderer_checkbox',
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

    public function getGridUrl() {
        $params = array(
            'id' => $this->getErpCustomer()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_customer_erpaccount/shipstatusgrid', $params);
    }

}
