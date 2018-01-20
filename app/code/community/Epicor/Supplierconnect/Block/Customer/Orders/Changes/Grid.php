<?php

/**
 * Supplier Changed order list grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Orders_Changes_Grid extends Epicor_Common_Block_Generic_List_Search {

    private $_allowEdit;
    
    public function __construct() {
        parent::__construct();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        
        $this->_allowEdit = $helper->customerHasAccess('Epicor_Supplierconnect','Orders','confirmchanges','','Access');
        
        $this->setId('supplierconnect_orders_changes');
        $this->setDefaultSort('purchase_order_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('spcs');
        $this->setIdColumn('purchase_order_number');
        $this->initColumns();
        
    }

    public function getRowUrl($row) {
        return false;
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

    protected function initColumns() {
        parent::initColumns();

        $columns = $this->getCustomColumns();

        $newColumns = array(
            'expand' => array(
                'header' => Mage::helper('epicor_comm')->__(''),
                'align' => 'left',
                'index' => 'expand',
                'type' => 'text',
                'column_css_class' => "expand-row",
                'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Changes_Renderer_Expand(),
                'filter' => false
            )
        );

        $columns = array_merge_recursive($newColumns, $columns);

        $columns['lines'] = array(
            'header' => Mage::helper('epicor_comm')->__(''),
            'align' => 'left',
            'index' => 'lines',
            'renderer' => new Epicor_Supplierconnect_Block_Customer_Orders_Changes_Renderer_Lines(),
            'type' => 'text',
            'filter' => false,
            'column_css_class' => "expand-content",
            'header_css_class' => "expand-content",
            'keep_data_format' => 1
        );

        $this->setCustomColumns($columns);
    }

}