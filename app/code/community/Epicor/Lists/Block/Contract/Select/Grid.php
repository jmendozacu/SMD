<?php

/**
 * Customer select page grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Select_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    const LIST_STATUS_ACTIVE = 'A';
    const LIST_STATUS_DISABLED = 'D';
    const LIST_STATUS_ENDED = 'E';
    const LIST_STATUS_PENDING = 'P';

    protected $_erp_customer;

    public function __construct()
    {
        parent::__construct();
        $this->setId('selectgrid');
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');
        $this->setCacheDisabled(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }

    protected function _prepareCollection()
    {
        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */
        $contracts = $helper->getActiveContracts();
        $contractIds = empty($contracts) ? array(0) : array_keys($contracts); 
        $collection = Mage::getModel('epicor_lists/list')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Collection */
        $collection->getSelect()->joinLeft(array('contracts' => $collection->getTable('epicor_lists/contract')), 'main_table.id = contracts.list_id', array('po_number' => 'contracts.purchase_order_number'), null);
        $collection->addFieldToFilter('main_table.id', array('in' => $contractIds));
        $collection->getSelect()->group('main_table.id');
        $collection->addFieldToFilter('main_table.type', array('eq' => 'Co'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml(true);

        $helper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $helper Epicor_Lists_Helper_Frontend_Contract */

        $allowed = $helper->allowedContractType();
        $required = $helper->requiredContractType();
        $isAjax = $this->getRequest()->getParam('ajax');

        if (($allowed == 'B' && in_array($required, array('E', 'O')) && !$isAjax) || ((in_array($required, array('O'))) && (!$isAjax))) {
            $url = Mage::getUrl('*/*/selectContract', array('contract' => -1));
            $message = $this->__('Continue without selecting a Contract');
            $html .= '<button title="' . $message . '" type="button" class="scalable" onclick="javascript:window.location=\'' . $url . '\'"><span><span>' . $message . '</span></span></button>';
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
    protected function _prepareColumns()
    {

        $this->addColumn(
            'title', array(
            'header' => $this->__('Title'),
            'index' => 'title',
            'filter_index' => 'title',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'erp_code', array(
            'header' => $this->__('Code'),
            'index' => 'erp_code',
            'filter_index' => 'erp_code',
            'type' => 'text',
            'renderer' => new Epicor_Lists_Block_Contract_Select_Grid_Renderer_Erpcode()
            )
        );


        $this->addColumn(
            'po_number', array(
            'header' => $this->__('PO Number'),
            'index' => 'po_number',
            'filter_index' => 'purchase_order_number',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'start_date', array(
            'header' => $this->__('Start Date'),
            'index' => 'start_date',
            'filter_index' => 'start_date',
            'type' => 'datetime'
            )
        );

        $this->addColumn(
            'end_date', array(
            'header' => $this->__('End Date'),
            'index' => 'end_date',
            'filter_index' => 'end_date',
            'type' => 'datetime'
            )
        );

        
        
        $selectAction = array(
            'caption' => Mage::helper('epicor_comm')->__('Select'),
            'url' => array('base' => '*/*/selectContract'),
            'field' => 'contract',
        );
        
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        if ($quote->hasItems()) {
            $message = Mage::helper('epicor_comm')->__('Changing Contract may remove items from the cart that are not valid for the selected Contract. Do you wish to continue?');
            $selectAction['confirm'] = $message;
        }
        
        $this->addColumn(
            'select', array(
            'header' => $this->__('Select'),
            'width' => '280',
            'index' => 'id',
            'renderer' => 'epicor_lists/contract_select_grid_renderer_select',
            'links' => 'true',
            'getter' => 'getId',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'actions' => array(
                $selectAction,
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View Products'),
                    'url' => array('base' => '*/*/productsgrid'),
                    'field' => 'contract',
                    'id' => 'productGridUrl',
                    'onclick' => "productSelector.openpopup('ecc_contract_products',this.href); return false;",
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View Address'),
                    'id' => 'addressGridUrl',
                    'url' => array('base' => '*/*/addressesgrid'),
                    'field' => 'contract',
                    'onclick' => "addressSelector.openpopup('ecc_contract_address',this.href); return false;",
                ),
            )
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/selectgrid', array('_current' => true));
    }

}
