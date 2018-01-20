<?php

/**
 * Customer access groups grid 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_List_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareCollection() {
        $erpAccount = Mage::registry('access_erp_account');
        /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */

        $collection = Mage::getModel('epicor_common/access_group')->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Group_Collection */
        $collection->addFieldToFilter(
                'erp_account_id', array(
            array('eq' => $erpAccount->getId()),
            array('null' => '')
                )
        );

        if ($erpAccount->isTypeSupplier()) {
            $collection->addFieldToFilter('type', 'supplier');
        } else if ($erpAccount->isTypeCustomer()) {
            $collection->addFieldToFilter('type', 'customer');
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('entity_name', array(
            'header' => Mage::helper('epicor_common')->__('Access Group'),
            'align' => 'left',
            'index' => 'entity_name'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return Mage::getUrl('*/*/editgroup', array('id' => $row->getId()));
    }

}
