<?php

/**
 * List Message Log Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Messagelog extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $list;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setId('messagelog_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('start_datestamp');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Gets the List for this tab
     *
     * @return boolean
     */
    public function getList()
    {
        if (!$this->list) {
            if (Mage::registry('list')) {
                $this->list = Mage::registry('list');
            } else {
                $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('id'));
            }
        }
        return $this->list;
    }

    /**
     * Is this tab shown?
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab Label
     *
     * @return boolean
     */
    public function getTabLabel()
    {
        return 'Message Log';
    }

    /**
     * Tab Title
     *
     * @return boolean
     */
    public function getTabTitle()
    {
        return 'Message Log';
    }

    /**
     * Is this tab hidden?
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Build data for List Message Log
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Messagelog
     */
    protected function _prepareCollection()
    {
        $contractCode = Mage::helper('epicor_comm/messaging')->getUom($this->getList()->getData('erp_code'));
        $accountNumber = Mage::helper('epicor_comm/messaging')->getSku($this->getList()->getData('erp_code'));
        $collection = Mage::getModel('epicor_comm/message_log')->getCollection();
        /* @var $collection Epicor_Comm_Model_Mysql4_Message_Log_Collection */
        $collection->addFieldToFilter('message_parent', Epicor_Comm_Model_Message::MESSAGE_TYPE_UPLOAD);
        $collection->addFieldToFilter('message_category', Epicor_Comm_Model_Message::MESSAGE_CATEGORY_LIST);
        if ($this->getList()->getType() == 'Co') {
            $collection->addFieldToFilter('message_subject', $accountNumber . '-' . $contractCode);
        } else {
            $collection->addFieldToFilter('message_subject', $this->getList()->getErpCode());
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Message Log
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Messagelog
     */
    protected function _prepareColumns()
    {

        $this->addColumn('message_type', array(
            'header' => $this->__('Message Type'),
            'align' => 'left',
            'index' => 'message_type',
            'renderer' => new Epicor_Comm_Block_Renderer_Message(),
        ));

        $this->addColumn('message_status', array(
            'header' => $this->__('Message Status'),
            'align' => 'left',
            'index' => 'message_status',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Widget_Grid_Column_Renderer_Messagestatus()
        ));

        $this->addColumn('message_subject', array(
            'header' => $this->__('Message Subject'),
            'align' => 'left',
            'index' => 'message_subject'
        ));

        $this->addColumn('start_datestamp', array(
            'header' => $this->__('Start Time'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'start_datestamp',
        ));

        $this->addColumn('duration', array(
            'header' => $this->__('Duration (ms)'),
            'align' => 'left',
            'index' => 'duration',
            'type' => 'number'
        ));

        $this->addColumn('status_code', array(
            'header' => $this->__('Status'),
            'align' => 'left',
            'index' => 'status_code'
        ));

        $this->addColumn('status_description', array(
            'header' => $this->__('Description'),
            'align' => 'left',
            'index' => 'status_description'
        ));

        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100',
            'type' => 'action',
            'renderer' => 'epicor_common/adminhtml_widget_grid_column_renderer_action',
            'links' => 'true',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('View'),
                    'url' => array(
                        'base' => 'adminhtml/epicorcomm_message_log/view',
                        'params' => array(
                            'source' => 'list',
                            'sourceid' => $this->getList()->getId())
                    ),
                    'field' => 'id'
                ),
                array(
                    'caption' => $this->__('Reprocess'),
                    'url' => array('base' => 'adminhtml/epicorcomm_message_log/reprocess',
                        'params' => array(
                            'source' => 'list',
                            'sourceid' => $this->getList()->getId()
                        )
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true,
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $params = array(
            'source' => 'list',
            'sourceid' => $this->getList()->getId(),
            'id' => $row->getId()
        );
        return $this->getUrl('adminhtml/epicorcomm_message_log/view', $params);
    }

    public function getGridUrl()
    {
        $params = array(
            'id' => $this->getList()->getId(),
            '_current' => true,
            'ajax' => true
        );
        return $this->getUrl('adminhtml/epicorlists_list/messageloggrid', $params);
    }

}
