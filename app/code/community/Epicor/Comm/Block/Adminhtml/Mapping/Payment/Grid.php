<?php

class Epicor_Comm_Block_Adminhtml_Mapping_Payment_Grid extends Epicor_Common_Block_Adminhtml_Mapping_Default_Filter
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('paymentmappingGrid');
      $this->setDefaultSort('code');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_comm/erp_mapping_payment')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('magento_code', array(
          'header'    => Mage::helper('epicor_comm')->__('Payment Method'),
          'align'     =>'left',
          'index'     => 'magento_code',
          'width'     => '50%',
          'type'      => 'options',
          'options'       => Mage::helper('payment')->getPaymentMethodList(true),
          'option_groups' => Mage::helper('payment')->getPaymentMethodList(true, true, true),       
      ));
      
      
      $this->addColumn('erp_code', array(
          'header'    => Mage::helper('epicor_comm')->__('ERP Code'),
          'align'     => 'left',
          'index'     => 'erp_code',
      ));
      
      $this->addColumn('payment_collected', array(
          'header'    => Mage::helper('epicor_comm')->__('Payment Collected'),
          'align'     => 'left',
          'index'     => 'payment_collected',
          'renderer'=> new Epicor_Comm_Block_Adminhtml_Mapping_Renderer_Paymentcollected()
      ));
      
      $this->addColumn('gor_trigger', array(
          'header'    => Mage::helper('epicor_comm')->__('Order Trigger'),
          'align'     => 'left',
          'index'     => 'gor_trigger',
      ));
      
      $this->addColumn('gor_online_prevent_repricing', array(
          'header'    => Mage::helper('epicor_comm')->__('Gor-On Prevent Repricing'),
          'align'     => 'left',
          'index'     => 'gor_online_prevent_repricing',
      ));
      
      $this->addColumn('gor_offline_prevent_repricing', array(
          'header'    => Mage::helper('epicor_comm')->__('Gor-Off Prevent Repricing'),
          'align'     => 'left',
          'index'     => 'gor_offline_prevent_repricing',
      ));
      
      $this->addColumn('bsv_online_prevent_repricing', array(
          'header'    => Mage::helper('epicor_comm')->__('Bsv-On Prevent Repricing'),
          'align'     => 'left',
          'index'     => 'bsv_online_prevent_repricing',
      ));
      
      $this->addColumn('bsv_offline_prevent_repricing', array(
          'header'    => Mage::helper('epicor_comm')->__('Bsv-Off Prevent Repricing'),
          'align'     => 'left',
          'index'     => 'bsv_offline_prevent_repricing',
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