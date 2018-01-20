<?php

/**
 * Access group rights list grid config 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Access_Management_Groups_Details_Rights_Grid extends Mage_Adminhtml_Block_Widget_Grid {

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
        
        $collection = Mage::getModel('epicor_common/access_right')->getCollection();
        /* @var $collection Epicor_Common_Model_Resource_Access_Right_Collection */

        if ($erpAccount->isTypeSupplier()) {
            $collection->addFieldToFilter('type', 'supplier');
        } else if ($erpAccount->isTypeCustomer()) {
            $collection->addFieldToFilter('type', 'customer');
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        if (!Mage::registry('access_group_global')) {
            $this->addColumn('entity_id', array(
                'header' => Mage::helper('epicor_common')->__('Selected'),
                'align' => 'center',
                'index' => 'entity_id',
                'type' => 'checkbox',
                'field_name' => 'rights[]',
                'values' => $this->_getSelectedValues(),
                'sortable' => false
            ));
        }

        $this->addColumn('entity_name', array(
            'header' => Mage::helper('epicor_common')->__('Access Right'),
            'align' => 'left',
            'index' => 'entity_name'
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

            $collection = Mage::getModel('epicor_common/access_group_right')->getCollection();
            /* @var $collection Epicor_Common_Model_Resource_Access_Group_Right_Collection */

            $collection->addFieldToFilter('group_id', $group->getId());
            foreach ($collection->getItems() as $element) {
                $this->_selected[] = $element->getRightId();
            }
        }

        return $this->_selected;
    }

}
