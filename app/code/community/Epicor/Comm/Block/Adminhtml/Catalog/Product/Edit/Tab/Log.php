<?php
/**
 * Product Message Log Grid
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Log extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function __construct() {
        parent::__construct();
        $this->setId('product_message_log');
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
    
    protected function _getProduct() {
        if(!Mage::registry('current_product')){
            Mage::register('current_product', Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id')));
        }          
        return Mage::registry('current_product');
        
    }

    protected function _prepareCollection() {
        $sku = $this->_getProduct()->getSku();
        $collection = Mage::getModel('epicor_comm/message_log')->getCollection();


        /* @var $collection Epicor_Comm_Model_Mysql4_Erp_Customer_Sku_Collection */
        $collection->addFieldToFilter('message_parent', Epicor_Comm_Model_Message::MESSAGE_TYPE_UPLOAD);
        $collection->addFieldToFilter('message_category', Epicor_Comm_Model_Message::MESSAGE_CATEGORY_PRODUCT);
        $collection->addFieldToFilter('message_subject', $sku);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('message_type', array(
            'header' => Mage::helper('epicor_comm')->__('Message Type'),
            'align' => 'left',
            'index' => 'message_type',
            'renderer' => new Epicor_Comm_Block_Renderer_Message(),
        ));

        $this->addColumn('message_status', array(
            'header' => Mage::helper('epicor_comm')->__('Message Status'),
            'align' => 'left',
            'index' => 'message_status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Messagestatus()
        ));

        $this->addColumn('message_secondary_subject', array(
            'header' => Mage::helper('epicor_comm')->__('Secondary Subject'),
            'align' => 'left',
            'index' => 'message_secondary_subject'
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
            'index' => 'duration',
            'type' => 'number'
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
        $this->addColumn('action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('View'),
                    'url' => array(
                        'base' => 'adminhtml/epicorcomm_message_log/view',
                        'params' => array(
                            'source' => 'product',
                            'sourceid' => $this->_getProduct()->getId()
                        )
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Reprocess'),
                    'url' => array('base' => 'adminhtml/epicorcomm_message_log/reprocess',
                        'params' => array(
                            'source' => 'product',
                            'sourceid' => $this->_getProduct()->getId()
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
//        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));  removed export buttons until problems resolved 
//        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));  have not removed actions from ErpaccountController

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        $params = array(
            'source' => 'product',
            'sourceid' => $this->_getProduct()->getId(),
            'id' => $row->getId(),
            'ajax'=>true    
        );
        return $this->getUrl('adminhtml/epicorcomm_message_log/view', $params);
    }

    public function getGridUrl() {
        $params = array(
            'id' => $this->_getProduct()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_message_log/grid', $params);  
    }
     public function getTabUrl()
    {
        return $this->getUrl('adminhtml/epicorcomm_message_log/grid', array('_current' => true));
    }
    public function getSkipGenerateContent()
   {
       return false;
   }
     public function getTabClass()
    {
        return 'ajax notloaded';
    }
}
