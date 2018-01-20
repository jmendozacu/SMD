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

class Blackbird_ContentManager_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reviewGrid');
        // This is the primary key of the database
        $this->setDefaultSort('review_id');
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
        $collection = Mage::getModel('contentmanager/review')
                ->getCollection();
        
        //load attributes value for selected store view
        foreach($collection as $item)
        {
            $item = $item->setStoreId($this->_getStore()->getId())->load();
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('contentmanager')->__('ID'),
            'align'     =>'center',
            'width'     => '20px',
            'index'     => 'review_id',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('contentmanager')->__('Content'),
            'align'     =>'left',
            'index'     => 'entity_id',
            'renderer'  => new Blackbird_ContentManager_Block_Adminhtml_Review_Renderer_Content(),
        ));
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('contentmanager')->__('Review'),
            'align'     =>'left',
            'index'     => 'comment',
        ));
 
        //$this->_prepareDynamicColumns();
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('contentmanager')->__('Status'),
            'align'     =>'left',
            'width'     => '100px',
            'index'     => 'status',
            'type'      => 'options',
            'options' => Mage::getSingleton('contentmanager/review_status')->getOptionArray(),
        ));
        
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('contentmanager')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '160px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_at',
        ));
 
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('contentmanager')->__('Update Time'),
            'align'     => 'left',
            'width'     => '160px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'updated_at',
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->getMassactionBlock()->setFormFieldName('review');
    
        $this->getMassactionBlock()->addItem('delete', array(
                'label'=> Mage::helper('contentmanager')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('contentmanager')->__('Are you sure?')
        ));
    
        $statuses = Mage::getSingleton('contentmanager/review_status')->getOptionArray();

        $this->getMassactionBlock()->addItem('status', array(
                'label'=> Mage::helper('contentmanager')->__('Change status'),
                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                        'visibility' => array(
                                'name' => 'status',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper('contentmanager')->__('Status'),
                                'values' => $statuses
                        )
                )
        ));
    
        Mage::dispatchEvent('adminhtml_contenttype_review_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
 
}