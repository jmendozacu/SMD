<?php

/**
 * Hosting Sites Grid
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Sites_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getModel('hostingmanager/site')->getCollection();
        /* @var $collection Epicor_HostingManager_Model_Resource_Site_Collection */

        $cert_table = $collection->getTable('hostingmanager/certificate');

        $collection->getSelect()->joinLeft(
                array('cert' => $cert_table), 'certificate_id=cert.entity_id', array(
            'cert_name' => 'name',
        ));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Sets up the stores for each row so we can differentiate stores display
     * between is_website y/n sites
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();

        $items = $this->getCollection()->getItems();

        $stores = array(Mage::app()->getDefaultStoreView()->getId());
        
        foreach ($items as $item) {
            if (!$item->getIsWebsite()) {
                $stores[] = $item->getChildId();
            }
        }

        foreach ($items as $item) {
            $item->setIgnoreStores($stores);
        }
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
            'header' => Mage::helper('hostingmanager')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('url', array(
            'header' => Mage::helper('hostingmanager')->__('Url'),
            'align' => 'left',
            'index' => 'url',
        ));

        $this->addColumn('stores', array(
            'header' => Mage::helper('hostingmanager')->__('Stores'),
            'align' => 'left',
            'index' => 'child_id',
            'renderer' => new Epicor_HostingManager_Block_Adminhtml_Sites_Column_Renderer_Stores(),
        ));

        $this->addColumn('cert_name', array(
            'header' => Mage::helper('hostingmanager')->__('SSL Status'),
            'align' => 'left',
            'index' => 'cert_name',
            'cert_id' => 'certificate_id',
            'renderer' => new Epicor_HostingManager_Block_Adminhtml_Sites_Column_Renderer_Ssl(),
            'show_name' => true,
            'filter_index' => 'cert.name'
        ));

        $this->addColumn('edit', array(
            'header' => Mage::helper('hostingmanager')->__(''),
            'width' => '50',
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
            'is_system' => true
        ));
        $this->addColumn('delete', array(
            'header' => Mage::helper('hostingmanager')->__(''),
            'width' => '50',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('hostingmanager')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'onclick' => 'return confirm('.Mage::helper('hostingmanager')->__("Are you sure you want to do this?").');'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'id',
            'is_system' => true
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
