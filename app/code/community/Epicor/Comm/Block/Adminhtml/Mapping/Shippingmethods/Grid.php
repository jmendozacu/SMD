<?php

class Epicor_Comm_Block_Adminhtml_Mapping_Shippingmethods_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('shippingmethodsgrid');
      $this->setDefaultSort('code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_comm/erp_mapping_shippingmethod')->getCollection();             
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('shipping_method', array(
          'header'    => Mage::helper('epicor_comm')->__('Shipping Method'),
          'align'     =>'left',
          'index'     => 'shipping_method',
     ));
      
      $this->addColumn('shipping_method_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Shipping Method Code'),
          'align'     =>'left',
          'index'     => 'shipping_method_code',
     ));
      
      $this->addColumn('erp_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Erp Code Value'),
          'align'     => 'left',
          'index'     => 'erp_code',
      ));
      
      $this->addColumn('tracking_url', array(
          'header'    => Mage::helper('epicor_comm')->__('Tracking Url'),
          'align'     => 'left',
          'index'     => 'tracking_url',
          'filter'    => false,
          'sortable'  => false,          
          'renderer' => new Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Trackingurl(),
      ));      
      
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('epicor_comm')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}