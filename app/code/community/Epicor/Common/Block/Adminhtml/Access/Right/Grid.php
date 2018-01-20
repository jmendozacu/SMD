<?php

class Epicor_Common_Block_Adminhtml_Access_Right_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('accessGroupGrid');
      $this->setDefaultSort('entity_name');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('epicor_common/access_right')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('entity_name', array(
          'header'    => Mage::helper('epicor_common')->__('Access Right'),
          'align'     =>'left',
          'index'     => 'entity_name'  
      ));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}