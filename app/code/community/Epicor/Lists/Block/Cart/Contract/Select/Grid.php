<?php

/**
 * Customer select page grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Cart_Contract_Select_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('selectlinecontractgrid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');

        $this->setSaveParametersInSession(false);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('epicor_common');
        $this->setIdColumn('id');

        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
        $this->setCacheDisabled(true);
        $this->setShowAll(false);
        $this->setUseAjax(true);
        $this->setSkipGenerateContent(true);
       
        $this->setCustomData($this->getContractData());
        
        $this->_emptyText = Mage::helper('adminhtml')->__('No Contracts available');
    }

    /**
     * Sorts out contract data to be displayed
     *
     * @return array
     */
    protected function getContractData()
    {
        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */

        $messageHelper = Mage::helper('epicor_comm/messaging');
        /* @var $messageHelper Epicor_Comm_Helper_Messaging */
        $item = Mage::registry('ecc_line_contract_item');
        /* @var $item Epicor_Comm_Model_Quote_Item */
        $product = $item->getProduct();

        $contractData = $helper->getContractsForCartItem($item);
        $productContracts = array();
        $contracts = array();
        foreach ($contractData as $contract) {
            /* @var $contract Epicor_Lists_Model_List */
            $productContracts[] = $contract->getErpCode();
            $contracts[$contract->getErpCode()] = $contract;
        }

        $product->setQty(1);
        $product->setMsqQty(1);
        $product->setEccContracts($productContracts);

        $products = array($product);

        $functions = array(
            'setTrigger' => array('custom_msq_send'),
            'addProducts' => array($products),
        );

        $messageHelper->sendErpMessage('epicor_comm', 'msq', array(), array(), $functions);

        $msqData = (array) $product->getEccMsqContractData();

        foreach ($msqData as $erpContract) {
            if ($erpContract) {
                $contract = isset($contracts[$erpContract->getContractCode()]) ? $contracts[$erpContract->getContractCode()] : false;
                if ($contract) {
                    $contract->setQuantity($erpContract->getMaximumContractQty());
                    $contract->setPrice($erpContract->getCustomerPrice());
                    $breaks = $erpContract->getBreaks() ? $erpContract->getBreaks()->getasarrayBreak() : array();
                    $contract->setPriceBreaks($breaks);
                    $contracts[$erpContract->getContractCode()] = $contract;
                }
            }
        }

        return $contracts;
    }

    protected function _toHtml()
    {
        $html = '';
        $item = Mage::registry('ecc_line_contract_item');
        $uomDelimiter = Mage::helper('epicor_comm/messaging_customer')->getUOMSeparator();
        $sku = implode(' ',explode($uomDelimiter, $item->getSku()));
        
        if ($this->getRequest()->getParam('noheader') == false) {
            $html .= '<h2 id="line-contract-select-header">' . $this->__("Select Line Contract") . '</h2>';
            $html .= '<span id="line-contract-select-width" style="">';
            $html .= '<span id="line-contract-select-product">' . $this->__("Product: {$sku}") . '</span>';
            $html .= '<button id="line-select-close-popup" title="' . $this->__('Close Popup') . '" type="button" class="scalable " onclick="lineContractSelect.closepopup()" style=""><span><span>' . $this->__('Close Popup') . '</span></span></button>';
            $html .= '</span>';
            
            }
        $html .= parent::_toHtml(true);

        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */
        if ($helper->requiredContractType() == 'O') {
            /* @var $item Epicor_Comm_Model_Quote_Item */
            $url = Mage::getUrl('epicor_lists/cart/applycontractselect', array('itemid' => $item->getId(), 'contract' => 0));
            $html .= '<button id="line-select-no-contract" title="' . $this->__('No Contract') . '" type="button" class="scalable" onclick="javascript:window.location=\'' . $url . '\'"><span><span>' . $this->__('Continue With No Contract') . '</span></span></button>';
        }

        return $html;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Build columns for List Contract
     *
     *
     */
    protected function _getColumns()
    {
        $columns = array(
            'title' => array(
                'header' => $this->__('Title'),
                'index' => 'title',
                'filter_index' => 'title',
                'sortable' => false,
                'type' => 'text'
            ),
            'erp_code' => array(
                'header' => $this->__('Description'),
                'index' => 'description',
                'filter_index' => 'description',
                'sortable' => false,
                'type' => 'text'
            ),
            'price' => array(
                'header' => $this->__('Price'),
                'index' => 'price',
                'filter_index' => 'price',
                'type' => 'number',
                'width' => 300,
                'sortable' => false,
                'renderer' => new Epicor_Lists_Block_Cart_Contract_Select_Renderer_Price
            ),
            'quantity' => array(
                'header' => $this->__('Quantity'),
                'index' => 'quantity',
                'filter_index' => 'quantity',
                'type' => 'text',
                'align' => 'center',
                'width' => 150,
                'sortable' => false,
            ),
            'end_date' => array(
                'header' => $this->__('End Date'),
                'index' => 'end_date',
                'filter_index' => 'end_date',
                'type' => 'datetime',
                'sortable' => false,
            ),
            'select' => array(
                'header' => $this->__('Select'),
                'index' => 'id',
                'type' => 'text',
                'filter' => false,
                'sortable' => false,
                'width' => 150,
                'renderer' => new Epicor_Lists_Block_Cart_Contract_Select_Renderer_Select
            )
        );

        return $columns;
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        $item = Mage::registry('ecc_line_contract_item');
        /* @var $item Epicor_Comm_Model_Quote_Item */
        return $this->getUrl('*/*/contractselectgrid', array('itemid' => $item->getId(), 'noheader' => 1));
    }

}
