<?php

/**
 * List Products Frontend Serialized Grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    private $_selected = array();

    public function _construct()
    {
        parent::_construct();

        $this->setId('productsGrid');
        $this->setUseAjax(true);
  //      $this->setSaveParametersInSession(false);

        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        $this->setDefaultFilter(array('selected_products' => 1));
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Close'),
                    'onclick' => 'productSelector.closepopup()',
                    'class' => 'task'
                ))
        );

        $urlRedirect = $this->getUrl('*/*/selectcontract', array('_current' => true, 'contract' => $this->getRequest()->getParam('contract')));
        $onClick = 'location.href=\'' . $urlRedirect . '\';';
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        /* @var $quote Epicor_Comm_Model_Quote */
        if ($quote->hasItems()) {
            $message = Mage::helper('epicor_comm')->__('Changing Contract may remove items from the cart that are not valid for the selected Contract. Do you wish to continue?');
            $onClick = 'if(confirm(\'' . $message . '\')) { ' . $onClick . ' }';
        }


        $this->setChild('select_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Select List'),
                    'onclick' => $onClick,
                    'class' => 'task'
                ))
        );
        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getSelectButtonHtml()
    {
        return $this->getChildHtml('select_button');
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getSelectButtonHtml();
        $html .= $this->getAddButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * Gets the List for this tab
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        $this->list = Mage::getModel('epicor_lists/list')->load($this->getRequest()->getParam('contract'));
        return $this->list;
    }
    
    /**
     * Row _LineFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _LineFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();
        if (!$filterroleid) {
            return $this;
        }
        $this->getCollection()->getSelect()
                ->where("cp.line_number like ?", "%" . $filterroleid . "%");

        return;
    }

    /**
     * Row _PartFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    protected function _PartFilter($collection, $column)
    {
        $filterroleid = $column->getFilter()->getValue();
        if (!$filterroleid) {
            return $this;
        }
        $this->getCollection()->getSelect()
                ->where("cp.part_number like ?", "%" . $filterroleid . "%");

        return;
    }
    
    /**
     * Build data for List Products
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products
     */
    protected function _prepareCollection()
    {
        $contract = $this->getRequest()->getParam('contract');
        $collection = Mage::getModel('catalog/product')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('uom');
        $collection->addAttributeToSelect('type_id');
        $collection->addAttributeToSelect('visibility');
        $collection->setFlag('no_product_filtering', true);
        $ids = $this->_getSelected();
        $stringIds = array_map('strval', $ids);
        $collection->addFieldToFilter('sku', array('in' => $stringIds));
        $collection->addFieldToFilter('type_id', array('neq' => 'grouped'));

        $collection->getSelect()->join(array('list' => $collection->getTable('epicor_lists/list_product')), 'e.sku = list.sku AND list.list_id = "' .$contract . '"', array('product_list_id'=>'list.id','qty'));
        $collection->getSelect()->join(array('contract' => $collection->getTable('epicor_lists/contract_product')), 'list.id = contract.list_product_id', array('start_date','line_number','part_number','end_date','contract_product_status'=>'status','min_order_qty', 'max_order_qty'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Build columns for List Products
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('epicor_lists');
        /* @var $helper Epicor_Lists_Helper_Data */

        $this->addColumn('product_list_id', array(           
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',           
        ));
//        $this->addColumn('start_date', array(           
//            'column_css_class'=> 'no-display',
//            'header_css_class'=> 'no-display',           
//        ));
//        $this->addColumn('end_date', array(           
//            'column_css_class'=> 'no-display',
//            'header_css_class'=> 'no-display',           
//        ));
        $this->addColumn('status', array(           
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',           
        ));
        $this->addColumn('visibility', array(           
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',           
        ));
        $this->addColumn(
            'sku', array(
            'header' => $helper->__('SKU'),
            'index' => 'sku',
            'filter_index' => 'sku',
            'type' => 'text',
            'sortable' => true,
            'renderer' => new Epicor_Lists_Block_Contract_Renderer_Skunodelimiter(),
            )
        );

        $this->addColumn(
            'uom', array(
            'header' => $helper->__('UOM'),
            'index' => 'uom',
            'filter_index' => 'uom',
            'type' => 'text',
            'sortable' => true,
            'filterable' => true,
            )
        );
        
         $this->addColumn(
            'type_id', array(
            'header' => $helper->__('Product Type'),
            'index' => 'type_id',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            'filter_index' => 'type_id',
            'sortable' => true,
            'filterable' => true,
            )
        );

        $this->addColumn(
            'product_name', array(
            'header' => $helper->__('Product Name'),
            'index' => 'name',
            'filter_index' => 'name',
            'type' => 'text'
            )
        );

        $this->addColumn(
            'line_number', array(
            'header' => $helper->__('Line Number'),
            'index' => 'line_number',
            'filter_index' => 'line_number',
            'type' => 'text',
            'sortable' => false,
            'filterable' => true,
            'filter_condition_callback' => array($this, '_LineFilter'),
            )
        );

        $this->addColumn(
            'part_number', array(
            'header' => $helper->__('Part Number'),
            'index' => 'part_number',
            'filter_index' => 'part_number',
            'type' => 'text',
            'sortable' => false,
            'filterable' => true,
            'filter_condition_callback' => array($this, '_PartFilter'),
            )
        );

        $this->addColumn(
            'start_date', array(
            'header' => $helper->__('Start Date'),
            'index' => 'start_date',
            'filter_index' => 'start_date',
            'type' => 'datetime',
            'sortable' => false,
            'filterable' => false,
            'filter' => false,
            )
        );

        $this->addColumn(
            'end_date', array(
            'header' => $helper->__('End Date'),
            'index' => 'end_date',
            'filter_index' => 'end_date',
            'type' => 'datetime',
            'sortable' => false,
            'filterable' => false,
            'filter' => false,
            )
        );

        $this->addColumn(
            'status_name', array(
            'header' => $helper->__('Status'),
      //    'index' => 'status_name',
      //    'filter_index' => 'status_name',
            'type' => 'options',
            'options' => $this->getStatusName(),    
            'renderer' => new Epicor_Lists_Block_Contract_Renderer_Status(),    
            'filter_condition_callback' => array($this, 'statusFilter'),    
            )
        );

        $this->addColumn(
            'max_order_qty', array(
            'header' => $helper->__('Quanities'),
            'index' => 'max_order_qty',
            'filter_index' => 'max_order_qty',
            'type' => 'options',
            'sortable' => false,
            'filterable' => false,
            'filter' => false,
            'renderer' => new Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Renderer_Contractquantities()
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Used in grid to return selected Products values.
     * 
     * @return array
     */
    protected function _getSelected()
    {
        return array_keys($this->getSelected());
    }

    /**
     * Builds the array of selected Products
     * 
     * @return array
     */
    public function getSelected()
    {

        $list = $this->getList();
        /* @var $list Epicor_Lists_Model_List */
        foreach ($list->getProducts() as $product) {
            $this->_selected[$product->getSku()] = array('sku' => $product->getSku());
        }

        return $this->_selected;
    }

    /**
     * Sets the selected items array
     *
     * @param array $selected
     *
     * @return void
     */
    public function setSelected($selected)
    {
        if (!empty($selected)) {
            foreach ($selected as $sku) {
                $this->_selected[$sku] = array('sku' => $sku);
            }
        }
    }

    /**
     * Gets grid url for ajax reloading
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', array('_current' => true));
    }

    /**
     * Row _uomFilter
     *
     * @param $collection, $column
     * 
     * @return null
     */
    public function _uomFilter($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        /* @var $delimiter epicor_lists_helper_messaging_customer */
        $delimiter = Mage::helper('epicor_lists/messaging_customer')->getUOMSeparator();
        // if unable to get a value of the column don't attempt filter 
        if (!$value) {
            return $this;
        }
        $this->getCollection()->getSelect()
            ->where("e.sku like ?", "%" . $delimiter . $value . "%");

        $collection->getSelect()->order('sku');
    }
    public function getStatusName() {
        $statusName = array('Active'=>'Active','Inactive'=>'Inactive', 'Expired'=>'Expired', 'Pending'=>'Pending', 'Not available on this Store'=>'Not available on this Store');
       
        return $statusName;
    }
    public function statusFilter($collection, $column) {
        
        if (!$value = $column->getFilter()->getValue()) {               // if unable to get a value of the column don't attempt filter  
            return $this;
        } 
        $nowTime = date( 'Y-m-d H:i:s', time());
        switch ($value) {
            case 'Not available on this Store':
//                may need to update this, as not really checking whether product has a parent, only that it is available and visibility is 'not visible individually'                  
//                $collection->getSelect()->joinLeft(array('link_table' => 'catalog_product_super_link'),
//                    'link_table.product_id = e.entity_id',
//                    array('product_id')
//                );
//                $collection->getSelect()->where('link_table.product_id IS NOT NULL');
                $collection->getSelect()->where("e.status = 1");
                $collection->getSelect()->where("e.visibility = 1");
                break;
            case 'Inactive':
                 $collection->getSelect()->where("contract.status = 0 OR contract.status IS NULL");
                break;
            case 'Pending':
                 $collection->getSelect()->where("contract.start_date IS NOT null AND contract.start_date > ?", $nowTime);
                break;
            case 'Expired':
                $collection->getSelect()->where("contract.end_date IS NOT null AND contract.end_date < ?", $nowTime);
                break;
            case 'Active':
                $collection->getSelect()->where("e.visibility <> 1");
                $collection->getSelect()->where("contract.status = 1");
                $collection->getSelect()->where("contract.start_date IS NOT null AND contract.start_date <= ?", $nowTime);
                $collection->getSelect()->where("contract.end_date IS NOT null AND contract.end_date >= ?", $nowTime);
                break;
            default:
                break;
        }      
        return $this;
    }	

}
