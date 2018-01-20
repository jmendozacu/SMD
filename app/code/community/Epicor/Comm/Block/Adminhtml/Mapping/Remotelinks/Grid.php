<?php

//class Epicor_Comm_Block_Adminhtml_Mapping_Remotelinks_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter
class Epicor_Comm_Block_Adminhtml_Mapping_Remotelinks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('remotelinksmappingGrid');
      $this->setDefaultSort('erp_code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_comm/erp_mapping_remotelinks')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('pattern_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Pattern Code'),
          'align'     =>'left',
          'index'     => 'pattern_code',
          'width'     => '200px',
          'align'     => 'left',          
      ));
      
      
      $this->addColumn('name', array(
          'header'    => Mage::helper('epicor_comm')->__('Name'),
          'align'     =>'right',
          'width'     => '100px',
          'index'     => 'name',
          'align'     => 'left',

      ));
      
      $this->addColumn('url_pattern', array(
          'header'    => Mage::helper('epicor_comm')->__('Url Pattern'),
          'align'     =>'right',
          'width'     => '400px',
          'index'     => 'url_pattern',
          'align'     => 'left',
      ));
      $this->addColumn('http_authorization', array(
          'header'    => Mage::helper('epicor_comm')->__('HTTP Authorization'),
          'align'     =>'right',
          'width'     => '200px',
           'renderer'  => new Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Remotelinks(),
          'index'     => 'http_authorization',
          'align'     => 'left',
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