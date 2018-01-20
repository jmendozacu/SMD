<?php

class Epicor_Comm_Block_Adminhtml_Sales_Returns_View_Tab_Details_Attachments_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_defaultLimit = 10000;
    
    public function __construct() {
        parent::__construct();
        $this->setId('attachments');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);

    }

    protected function _prepareCollection() {
        $return = Mage::registry('return');
        /* @var $return Epicor_Comm_Model_Customer_Return */

        $attData = Mage::getModel('epicor_comm/customer_return_attachment')->getCollection();
        /* @var $attData Epicor_Comm_Model_Mysql4_Customer_Return_Attachment_Collection */
        $attData->addFieldToFilter('return_id', array('eq' => $return->getId()));
        $attData->addFieldToFilter('line_id', array('null' => true));
        
        foreach ($attData->getItems() as $row) {
            $attachment = Mage::getModel('epicor_common/file')->load($row->getAttachmentId());
            /* @var $attachment Epicor_Common_Model_File */
            $attachment->setAttachmentLink($row);
            $attData->removeItemByKey($row->getId());
            $attData->addItem($attachment);
        }
        $this->setCollection($attData);

        return parent::_prepareCollection();
    }

    public function getRowUrl($row) {
        return false;
    }

    protected function _prepareColumns() {

        $columns = array();

        $columns['description'] = array(
            'header' => Mage::helper('epicor_comm')->__('Description'),
            'align' => 'left',
            'index' => 'description',
            'type' => 'text',
            'renderer' => new Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_Description(),
            'filterable' => false,
            'sortable' => false,
        );

        $columns['filename'] = array(
            'header' => Mage::helper('epicor_comm')->__('Filename'),
            'align' => 'left',
            'index' => 'filename',
            'type' => 'text',
            'renderer' => new Epicor_Comm_Block_Customer_Returns_Attachment_Lines_Renderer_File(),
            'filterable' => false,
            'sortable' => false,
        );


        $this->addColumn('description', $columns['description']);
        $this->addColumn('filename', $columns['filename']);

        parent::_prepareColumns();
    }

}
