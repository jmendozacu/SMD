<?php

/**
 * Access management group contact grid config
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_Details_Contacts_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_selected;

    public function __construct() {
        parent::__construct();
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setDefaultLimit('all');
    }

    protected function _prepareCollection() {
        $erpAccount = Mage::registry('access_erp_account');
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
        
        $collection = Mage::getResourceModel('customer/customer_collection');
        /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */

        $collection->addNameToSelect();
        $collection->addAttributeToSelect('email');
        if ($erpAccount->isTypeSupplier()) {
            $collection->addAttributeToSelect('supplier_erpaccount_id');
            $collection->addAttributeToFilter('supplier_erpaccount_id', $erpAccount->getId());
        } else if ($erpAccount->isTypeCustomer()) {
            $collection->addAttributeToSelect('erpaccount_id');
            $collection->addAttributeToFilter('erpaccount_id', $erpAccount->getId());
        }

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection->addFieldToFilter('entity_id', array('neq' => $customerId));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('epicor_common')->__('Selected'),
            'align' => 'center',
            'index' => 'entity_id',
            'type' => 'checkbox',
            'field_name' => 'contacts[]',
            'values' => $this->_getSelectedValues(),
            'sortable'  => false
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('epicor_common')->__('Name'),
            'align' => 'left',
            'index' => 'name'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('epicor_common')->__('Email Address'),
            'align' => 'left',
            'index' => 'email'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return null;
    }

    /**
     * Works out which values to select for this grid
     * 
     * @return array
     */
    private function _getSelectedValues() {
        if (empty($this->_selected)) {
            $this->_selected = array();

            $group = Mage::registry('access_group');
            /* @var $group Epicor_Common_Model_Access_Group */

            $collection = Mage::getModel('epicor_common/access_group_customer')->getCollection();
            /* @var $collection Epicor_Common_Model_Resource_Access_Group_Customer_Collection */
            $collection->addFieldToFilter('group_id', $group->getId());

            foreach ($collection->getItems() as $element) {
                $this->_selected[] = $element->getCustomerId();
            }
        }

        return $this->_selected;
    }

}
