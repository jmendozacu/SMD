<?php

/**
 * Erp Account Log list
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Log extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_erp_customer;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setId('erpaccount_logs');
        $this->setUseAjax(true);
        $this->setDefaultSort('start_datestamp');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * 
     * @return Epicor_Comm_Model_Location
     */
    public function getLocation()
    {
        if (!$this->_location) {
            $this->_location = Mage::registry('location');
        }
        return $this->_location;
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Upload Logs';
    }

    public function getTabTitle()
    {
        return 'Upload Logs';
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/message_log')->getCollection();
        $erpCode = $this->getLocation()->getCode();

        /* @var $collection Epicor_Comm_Model_Mysql4_Erp_Customer_Sku_Collection */
        $collection->addFieldToFilter('message_parent', Epicor_Comm_Model_Message::MESSAGE_TYPE_UPLOAD);
        $collection->addFieldToFilter('message_category', Epicor_Comm_Model_Message::MESSAGE_CATEGORY_LOCATION);
        $collection->addFieldToFilter('message_subject', array('like' => '%' . $erpCode . '%'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
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
                            'source' => 'location',
                            'sourceid' => $this->getLocation()->getId())
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Reprocess'),
                    'url' => array('base' => 'adminhtml/epicorcomm_message_log/reprocess',
                        'params' => array(
                            'source' => 'customer',
                            'sourceid' => $this->getLocation()->getId()
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

    public function getRowUrl($row)
    {
        $params = array(
            'source' => 'customer',
            'sourceid' => $this->getLocation()->getId(),
            'id' => $row->getId()
        );
        return $this->getUrl('adminhtml/epicorcomm_message_log/view', $params);
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getLocation()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorcomm_locations/loggrid', $params);
    }

}
