<?php


class Epicor_Comm_Block_Adminhtml_Sales_Order_View_Addproducts_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sales_order_view_addproducts_search';
    $this->_blockGroup = 'epicor_comm';
    $this->_headerText = Mage::helper('epicor_comm')->__('Add Products');
   // $this->_addButtonLabel = Mage::helper('epicor_comm')->__('Add Products To Order');
    
    $this->addButton(10, array('label'=>'Add Products To Order','onclick'=>"addProduct.addProducts()", 'class'=>"add"), 1);
    $this->addButton(20, array('label'=>'Cancel','onclick'=>"addProduct.closeProductSearch()"), 1);
    
    parent::__construct();
    $this->removeButton('add');
  }
}