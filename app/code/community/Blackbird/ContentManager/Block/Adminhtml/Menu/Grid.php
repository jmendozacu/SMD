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

class Blackbird_ContentManager_Block_Adminhtml_Menu_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('menuGrid');
        // This is the primary key of the database
        $this->setDefaultSort('menu_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('contentmanager/menu')->getCollection();
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);
        parent::_prepareCollection();
        
        return $this;
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('contentmanager')->__('ID'),
            'align'     =>'center',
            'width'     => '20px',
            'index'     => 'menu_id',
        ));
        
        $this->addColumn('identifier', array(
            'header'    => Mage::helper('contentmanager')->__('Identifier'),
            'align'     =>'left',
            'index'     => 'identifier',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('contentmanager')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        if (!Mage::app()->isSingleStoreMode() && Mage::helper('contentmanager')->isMenuAllowed(0)) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('contentmanager')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        }
        
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
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('contentmanager')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '160px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_at',
        ));
 
        $this->addColumn('update_at', array(
            'header'    => Mage::helper('contentmanager')->__('Update Time'),
            'align'     => 'left',
            'width'     => '160px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'update_at',
        ));
        
        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('menu_id' => $row->getId()));
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
}