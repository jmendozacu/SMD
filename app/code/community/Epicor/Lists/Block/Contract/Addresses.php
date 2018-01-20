<?php

/**
 * List Addresses Serialized Grid Frontend
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Addresses extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();

    public function __construct()
    {
        parent::__construct();
        $this->setId('addressesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }
    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('contract'));
        return $this->list;
    }


    protected function _prepareLayout()
    {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('adminhtml')->__('Close'),
                            'onclick' => 'addressSelector.closepopup()',
                            'class' => 'task'
                        ))
        );
        
        
        $urlRedirect = $this->getUrl('*/*/selectcontract', array('_current' => true, 'contract' => $this->getRequest()->getParam('contract')));
        $onClick = 'location.href=\'' . $urlRedirect . '\';';
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        if ($quote->hasItems()) {
            $message = Mage::helper('epicor_comm')->__('Changing Contract may remove items from the cart that are not valid for the selected Contract. Do you wish to continue?');
            $onClick = 'if(confirm(\'' . $message . '\')) { ' . $onClick . ' }';
        }


        $this->setChild('select_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Select List'),
                    'onclick' => $onClick,
                    'class' => 'task'
                ))
        );
        
        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getSelectButtonHtml()
    {
        return $this->getChildHtml('select_button');
    }    

    public function getMainButtonsHtml()
    {   
        $html  = $this->getSelectButtonHtml();
        $html .= $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }  

    /**
     * Build data for List Addresses
     *
     * 
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_lists/list_address')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Address_Collection */
        $collection->addFieldToFilter('list_id', $this->getList()->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Addresses
     *
     * 
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn(
            'address_code', array(
            'header' => $helper->__('Address Code'),
            'index' => 'address_code',
            'type' => 'text'
            )
        );
        
        $this->addColumn(
            'purchase_order_number', array(
            'header' => $helper->__('Purchase Order Number'),
            'index' => 'purchase_order_number',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'address_name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'flatt_address', array(
            'header' => $helper->__('Address'),
            'index' => 'address1',
            'type' => 'text',
            'renderer' => new Epicor_Lists_Block_Contract_Renderer_Address(),
            'filter_condition_callback' => array($this, '_addressFilter'),
            )
        );

        $this->addColumn(
            'address_email_address', array(
            'header' => $helper->__('Email'),
            'index' => 'email_address',
            'type' => 'text'
            )
        );
        
        $this->addColumn(
            'status', array(
            'header' => $helper->__('Status'),
            'type' => 'options',
            'options' => $this->getStatusName(),    
            'renderer' => new Epicor_Lists_Block_Contract_Renderer_Addressactive(),           
            'filter_condition_callback' => array($this, 'statusFilter')
            )
        );


        return parent::_prepareColumns();
    }

    protected function _addressFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $clone = clone $collection;

        $filterIds = array();
        foreach ($clone->getItems() as $item) {
            /* @var $item Epicor_Lists_Model_List */
            if (stripos($item->getFlattenedAddress(), $value) !== false) {
                $filterIds[] = $item->getId();
            }
        }

        $collection->addFieldToFilter('id', array('in' => $filterIds));
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/addressesgrid', array('_current' => true));
    }

    
    public function isSectionEditable()
    {
        return $this->getList()->getTypeInstance()->isSectionEditable('addresses');
    }
    
     public function getStatusName() {

        $statusName = array('Active' => 'Active', 'Inactive' => 'Inactive', 'Expired' => 'Expired');
        return $statusName;
    }

    public function statusFilter($collection, $column) {

        if (!$value = $column->getFilter()->getValue()) {   // if unable to get a value of the column don't attempt filter  
            return $this;
        }
        switch ($value) {

            case 'Inactive':
                $collection->addFieldToFilter('activation_date', array(
                    'neq' => null
                ));
                $collection->addFieldToFilter('activation_date', array(
                    'gteq' => now()
                ));

                break;

            case 'Expired':
                $collection->addFieldToFilter('expiry_date', array(
                    'neq' => null
                ));
                $collection->addFieldToFilter('expiry_date', array(
                    'lt' => now()
                ));

                break;

            case 'Active':
                $collection->addFieldToFilter('expiry_date', array(
                    'null' => true
                ));
                $collection->addFieldToFilter('activation_date', array(
                    'null' => true
                ));
                break;

            default:
                break;
        }
        return $this;
    }

}