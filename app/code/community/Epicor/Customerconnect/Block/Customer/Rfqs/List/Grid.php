<?php

/**
 * Customer RFQ list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    private $_allowEdit;

    public function __construct()
    {
        parent::__construct();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $this->_allowEdit = $helper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'confirmreject', '', 'Access');
        if ($this->_allowEdit) {
            $helper = Mage::helper('epicor_comm/messaging');
            /* @var $helper Epicor_Comm_Helper_Messaging */
            $this->_allowEdit = $helper->isMessageEnabled('customerconnect', 'crqc');
        }
        
        $this->setId('customerconnect_rfqs');
        $this->setDefaultSort('quote_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('crqs');
        $this->setIdColumn('quote_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportRfqsCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportRfqsXml'));
    }

    public function getRowUrl($row)
    {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        
        $msgHelper = Mage::helper('epicor_comm/messaging');
        /* @var $msgHelper Epicor_Comm_Helper_Messaging */
        $enabled = $msgHelper->isMessageEnabled('customerconnect', 'crqd');
        
        if ($enabled && $accessHelper->customerHasAccess('Epicor_Customerconnect', 'Rfqs', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNum = $helper->getErpAccountNumber();

            $quoteDetails = array(
                'erp_account' => $erpAccountNum,
                'quote_number' => $row->getQuoteNumber(),
                'quote_sequence' => $row->getQuoteSequence()
            );

            $requested = $helper->urlEncode($helper->encrypt(serialize($quoteDetails)));
            $url = Mage::getUrl('*/*/details', array('quote' => $requested));
        }

        return $url;
    }

    protected function initColumns()
    {
        parent::initColumns();

        $columns = $this->getCustomColumns();
        
        if(Mage::helper('epicor_lists/frontend_contract')->contractsDisabled()){
            unset($columns['contracts_contract_code']);
        }
        
        if ($this->_allowEdit) {
            $newColumns = array(
                'confirm' => array(
                    'header' => Mage::helper('customerconnect')->__('Confirm'),
                    'align' => 'left',
                    'index' => 'confirm',
                    'type' => 'text',
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_List_Renderer_Confirm(),
                    'filter' => false
                ),
                'reject' => array(
                    'header' => Mage::helper('customerconnect')->__('Reject'),
                    'align' => 'left',
                    'index' => 'reject',
                    'type' => 'text',
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Rfqs_List_Renderer_Reject(),
                    'filter' => false
                )
            );
            
            $columns = array_merge_recursive($newColumns, $columns);
        }

        $this->setCustomColumns($columns);
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        if ($this->_allowEdit) {
            $html .= '<div class="button">
                <div id="running_total" style="display:none"><p>' . $this->__('Total Confirmed') . ': <span id="running_total_price"></span></p></div>
                <button id="rfq_confirmreject_save" class="scalable" type="button">' . $this->__('Confirm / Reject RFQs') . '</button>
            </div>';
        }

        return $html;
    }
    
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        
        $items = $this->getCollection()->getItems();
        if (is_array($items)) {
            foreach ($items as $item) {
                $helper->sanitizeData($item);
            }
        }
    }

}
