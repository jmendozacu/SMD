<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:44
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Tag_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct(){
        parent::__construct();
        $this->setId('galleryTagGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('gallery/gallery_tag')->getCollection()
            ->addFieldToSelect('*');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('gallery')->__('Id'),
            'index'     => 'entity_id',
            'type'      => 'number'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('gallery')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('gallery')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('gallery')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
            ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('gallery')->__('Status'),
            'index'     => 'status',
            'width'     => '200',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('gallery')->__('Enabled'),
                '0' => Mage::helper('gallery')->__('Disabled'),
            )
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('gallery')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('gallery')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('gallery')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _getStore(){
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('gallery_tag');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('gallery')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('gallery')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('gallery')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('gallery')->__('Status'),
                    'values' => array(
                        '1' => Mage::helper('gallery')->__('Enabled'),
                        '0' => Mage::helper('gallery')->__('Disabled'),
                    )
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}