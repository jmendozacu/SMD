<?php
/**
 * Product Customer SKU Grid
 *
 * @author David.Wylie
 */
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Customersku 
extends Mage_Adminhtml_Block_Widget_Grid
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->setId('product_customersku');
        $this->setUseAjax(true);
        $this->setDefaultSort('product_sku');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
    
    public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Customer SKU';
    }

    public function getTabTitle() {
        return 'Customer SKU';
    }

    public function isHidden() {
        return false;
    }
    
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }
    
    protected function _prepareCollection() {
       $collection = Mage::getModel('epicor_comm/customer_sku')->getCollection(); 
    /* @var $collection Epicor_Comm_Model_Mysql4_Customer_Sku_Collection */
        $collection->getProductSelect();
        $collection->addFieldToFilter('product_id', $this-> _getProduct()->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
   
        $this->addColumn('erp_code', array(
            'header'    => Mage::helper('epicor_comm')->__('Customer'),
            'width'     => '150',
            'index'     => 'erp_code'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('epicor_comm')->__('Sku'),
            'width'     => '150',
            'index'     => 'sku',
            'filter_index' => 'main_table.sku'
        ));
        $this->addColumn('description', array(
            'header'    => Mage::helper('epicor_comm')->__('Description'),
            'index'     => 'description'
        ));
          $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('epicor_comm')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('epicor_comm')->__('Delete'),
                        'url'       => array('base'=> 'adminhtml/epicorcomm_message_ajax/deletecpnproduct',
                            'params'=>array('product'=>$this->getRequest()->getParam('id'))
                            ),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
//        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV')); export temporarily commented out  
//        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML')); related actions not removed from controller

        return parent::_prepareColumns();
    }
    
    public function getGridUrl() {
        $params = array(
            'id' => $this->_getProduct()->getId(),
            '_current' => true,
        );
        return $this->getUrl('adminhtml/epicorcomm_catalog_product/skugrid', $params);
    }
}
