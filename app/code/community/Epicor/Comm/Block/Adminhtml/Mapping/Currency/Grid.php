<?php

class Epicor_Comm_Block_Adminhtml_Mapping_Currency_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('currencymappingGrid');
      $this->setDefaultSort('erp_code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_comm/erp_mapping_currency')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('magento_id', array(
          'header'    => Mage::helper('epicor_comm')->__('Magento Description'),
          'align'     =>'left',
          'index'     => 'magento_id',
          'renderer'  => new Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency()
          
      ));
      
      
      $this->addColumn('magento_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Magento Code'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'magento_id',

      ));

      
      $this->addColumn('erp_code', array(
          'header'    => Mage::helper('epicor_comm')->__('ERP Currency Code'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'erp_code',
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
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}