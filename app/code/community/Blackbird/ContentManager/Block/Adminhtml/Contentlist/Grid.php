<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Block_Adminhtml_Contentlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('contentlistGrid');
        // This is the primary key of the database
        $this->setDefaultSort('cl_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('contentmanager/contentlist')->getCollection();
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('cl_id', array(
            'header'    => Mage::helper('contentmanager')->__('ID'),
            'align'     =>'center',
            'width'     => '20px',
            'index'     => 'cl_id',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('contentmanager')->__('Title'),
            'align'     =>'left',
            'width'     => '130px',
            'index'     => 'title',
        ));
        $this->addColumn('ct_id', array(
            'header'    => Mage::helper('contentmanager')->__('Content Type'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'ct_id',
            'type'      => 'options',
            'options'     => Blackbird_ContentManager_Model_Source_Options_Type::arrayOfContentTypes()
        ));
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('contentmanager')->__('URL'),
            'align'     => 'left',
            'width'     => '130px',
            'type'      => 'text',
            'index'     => 'url_key',
        ));
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('contentmanager')->__('URL'),
            'align'     => 'left',
            'width'     => '130px',
            'type'      => 'text',
            'index'     => 'url_key',
        ));
        $this->addColumn('store_id', array(
                'header'        => Mage::helper('contentmanager')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'width'     => '130px',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('contentmanager')->__('Status'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('contentmanager')->__('Disabled'),
                1 => Mage::helper('contentmanager')->__('Enabled')
            ),
        ));
 
        return parent::_prepareColumns();
    }
     
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('cl_id' => $row->getId()));
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
 
}