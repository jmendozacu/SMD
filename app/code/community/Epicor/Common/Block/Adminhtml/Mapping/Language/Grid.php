<?php

class Epicor_Common_Block_Adminhtml_Mapping_Language_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('languagemappingGrid');
      $this->setDefaultSort('erp_code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_common/erp_mapping_language')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      
      $this->addColumn('erp_code', array(
          'header'    => Mage::helper('epicor_common')->__('ERP Language Code'),
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'erp_code',
      ));
      
      $this->addColumn('languages', array(
          'header'    => Mage::helper('epicor_common')->__('Languages'),
          'align'     => 'left',
//          'width'     => '50px',
          'index'     => 'languages',

      ));
      
      $this->addColumn('language_codes', array(
          'header'    => Mage::helper('epicor_common')->__('Language Codes'),
          'align'     => 'left',
          'width'     => '150px',
          'index'     => 'language_codes',

      ));

      
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('epicor_common')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_common')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('epicor_common')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('epicor_common')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('epicor_common')->__('XML'));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}