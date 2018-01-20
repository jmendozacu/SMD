<?php

/**
 * Certificates grid block
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Certificates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        $this->setId('entity_id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('hostingmanager/certificate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('request', array(
            'header' => Mage::helper('hostingmanager')->__('R'),
            'align' => 'center',
            'index' => 'request',
            'width' => '30px',
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            'tick_mode' => 'content',
            'filter' => false
        ));

        $this->addColumn('private_key', array(
            'header' => Mage::helper('hostingmanager')->__('K'),
            'align' => 'center',
            'index' => 'private_key',
            'width' => '30px',
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            'tick_mode' => 'content',
            'filter' => false
        ));

        $this->addColumn('certificate', array(
            'header' => Mage::helper('hostingmanager')->__('C'),
            'align' => 'center',
            'index' => 'certificate',
            'width' => '30px',
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            'tick_mode' => 'content',
            'filter' => false
        ));

        $this->addColumn('c_a_certificate', array(
            'header' => Mage::helper('hostingmanager')->__('A'),
            'align' => 'center',
            'index' => 'c_a_certificate',
            'width' => '30px',
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            'tick_mode' => 'content',
            'filter' => false
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('hostingmanager')->__('Status'),
            'align' => 'center',
            'width' => '30px',
            'cert_id' => 'entity_id',
            'renderer' => new Epicor_HostingManager_Block_Adminhtml_Sites_Column_Renderer_Ssl(),
            'filter' => false
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('hostingmanager')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('hostingmanager')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('hostingmanager')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
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
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
