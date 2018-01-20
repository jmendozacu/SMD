<?php

/**
 * Supplier Parts list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_New_Grid extends Epicor_Common_Block_Generic_List_Search {

    protected $_configLocation = 'newpogrid_config';
    private $_allowEdit;

    public function __construct() {
        parent::__construct();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        
        $this->_allowEdit = $helper->customerHasAccess('Epicor_Supplierconnect','Orders','confirmnew','','Access');
        
        $this->setId('supplierconnect_orders_new');
        $this->setDefaultSort('purchase_order_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('spos');
        $this->setIdColumn('purchase_order_number');
        $this->initColumns();

        $filter = array(
            new Varien_Object(
                array(
                    'field' => 'confirm_via',
                    'value' => array('eq' => 'NEW')
                )
            ),
            new Varien_Object(
                array(
                    'field' => 'order_confirmed',
                    'value' => array('eq' => 'NC'),
                )
            ),
            new Varien_Object(
                array(
                    'field' => 'order_status',
                    'value' => array('eq' => 'O'),
                )
            )
        );

        $this->setAdditionalFilters($filter);
    }

    public function getRowUrl($row) {

        return false;
        $helper = Mage::helper('supplierconnect');
        /* @var $helper Epicor_Supplierconnect_Helper_Data */
        $erp_account_number = $helper->getSupplierAccountNumber();
        $requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getId()));
        return Mage::getUrl('*/*/details', array('order' => $requested));
    }

    protected function _toHtml() {
        $html = parent::_toHtml();

        if($this->_allowEdit) {
            $html .= '<div class="">    
                        <button id="purchase_order_confirmreject_save" class="scalable" type="button">Confirm / Reject PO</button>
            </div>';
        }

        return $html;
    }

}