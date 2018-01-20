<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Tab_Log 
extends Mage_Adminhtml_Block_Widget_Grid 
implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setId('order_message_log');
        $this->setDefaultSort('start_datestamp');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }
    
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Messaging Log';
    }

    public function getTabTitle() {
        return 'Messaging Log';
    }

    public function isHidden() {
        return false;
    }

    /**
     * 
     * @return Mage_Sales_Model_Order
     */
    public function getOrder(){
         if(!Mage::registry('current_order')){
            Mage::register('current_order', Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
        }          
        return Mage::registry('current_order');
    }

    protected function _prepareCollection() {
        $orderId = $this->getOrder()->getQuoteId();
        
        $collection = Mage::getModel('epicor_comm/message_log')->getCollection();


        /* @var $collection Epicor_Comm_Model_Mysql4_Erp_Customer_Sku_Collection */
        $collection->addFieldToFilter('message_category', Epicor_Comm_Model_Message::MESSAGE_CATEGORY_ORDER);
        $collection->addFieldToFilter('message_secondary_subject', array('like' => '%Basket Quote ID: '.$orderId.'%'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('message_type', array(
            'header' => Mage::helper('epicor_comm')->__('Message Type'),
            'align' => 'left',
            'index' => 'message_type'
        ));

        $this->addColumn('message_status', array(
            'header' => Mage::helper('epicor_comm')->__('Message Status'),
            'align' => 'left',
            'index' => 'message_status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Messagestatus()
        ));
        $this->addColumn('message_subject', array(
            'header' => Mage::helper('epicor_comm')->__('Subject'),
            'align' => 'left',
            'index' => 'message_subject'
        ));

        $this->addColumn('start_datestamp', array(
            'header' => Mage::helper('epicor_comm')->__('Start Time'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'start_datestamp',
        ));

        $this->addColumn('duration', array(
            'header' => Mage::helper('epicor_comm')->__('Duration (ms)'),
            'align' => 'left',
            'index' => 'duration'
        ));

        $this->addColumn('status_code', array(
            'header' => Mage::helper('epicor_comm')->__('Status'),
            'align' => 'left',
            'index' => 'status_code'
        ));

        $this->addColumn('status_description', array(
            'header' => Mage::helper('epicor_comm')->__('Description'),
            'align' => 'left',
            'index' => 'status_description'
        ));
        
        $this->addColumn('url', array(
            'header' => Mage::helper('epicor_comm')->__('Url'),
            'align' => 'left',
            'type' => 'text',
            'index' => 'url',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Logurl(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View'),
                    'url' => array('base' => 'adminhtml/epicorcomm_message_log/view',
                        'params' => array(
                            'source' => 'order',
                            'sourceid' => $this->getOrder()->getId()
                        )
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row) {
        $params = array(
            'source' => 'order',
            'sourceid' => $this->getOrder()->getId(),
            'id' => $row->getId()
        );
        return $this->getUrl('adminhtml/epicorcomm_message_log/view', $params);
    } 
     public function getGridUrl() {
         return $this->getUrl('adminhtml/epicorcomm_sales_order/loggrid', array('_current' => true)); 
    }
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/epicorcomm_sales_order/loggrid', array('_current' => true));  
    }
    public function getSkipGenerateContent()
   {
       return false;
   }
    public function getTabClass()
    {
        return 'ajax notloaded';
//        return 'ajax';
//        return 'ajax notloaded';
    }

}


