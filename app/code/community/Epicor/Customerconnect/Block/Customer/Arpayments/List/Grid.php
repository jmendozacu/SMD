<?php

/**
 * Customer AR Payments list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_List_Grid extends Epicor_Common_Block_Generic_List_Search {
    
    protected $_countTotals = true;

    public function __construct() {
        parent::__construct();
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_arpayments');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('caps');
        $this->setIdColumn('invoice_number');
        $this->setCacheDisabled(false);
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->initColumns();
        $checkAgedFilter = $this->returnDefaultFilter();
        $this->setDefaultFilter(array(
            'aged_period_number' => $checkAgedFilter
        ));
        $jsObjectName = $this->getJsObjectName();
        //When the user click's on reset button all the grid values will get removed
        //customerconnect_arpayments_aged_gridJsObject.resetFilter();
        $this->setAdditionalJavaScript("
            $jsObjectName.resetFilter = function() {
                this.addVarToUrl(this.sortVar, '');
                this.addVarToUrl(this.dirVar,  '');                
                this.reload(this.addVarToUrl(this.filterVar, ''));
                //window.history.pushState('object or string', 'Title', '/' + 'customerconnect/arpayments/');
                setTimeout(function() {
                    customerconnect_arpayments_aged_gridJsObject.resetFilter();
                }, 1000);                
               
               
            }          
        ");
    }

    public function getRowUrl($row) {
        return false;
    }
    
    
    public function returnDefaultFilter()
    {
        $columnId      = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        $columnIdParam = $this->getRequest()->getParam('agedfilter');
        $values        = explode('_', $columnId);
        $valuesParam   = explode('_', $columnIdParam);
        $checkEmpty    = '';
        if (($values[0] == "aged") || ($valuesParam[0] == "aged")) {
            $resetSort    = $this->getParam('dir');
            $assignVals   = ($columnIdParam) ? $valuesParam : $values;
            $lastNumber   = end($assignVals);
            $assignNumber = (int) $lastNumber;
            $checkEmpty   = !empty($assignNumber) ? $assignNumber : '';
            if (!empty($assignNumber)) {
                $this->setInternalFilter(true);
            }
        }
        return $checkEmpty;
        
    }    

    
    protected function initColumns()
    {
        parent::initColumns();

        $columns = $this->getCustomColumns();
        $newColumns['select_arpayments'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Select'),
                    'type' => 'text',
                    'index' => 'select_arpayments',
                    'filter' => false,
                    'class' =>'validate-number',
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Select(),
                    'is_system' => true
        );         
            
        $columns = array_merge_recursive($newColumns, $columns);
        
        $columns['settlement_discount'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Term Amount'),
                    'type' => 'text',
                    'index' => 'settlement_discount',
                    'filter' => false,
                    'class' =>'validate-number',
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_OutstandingAmount(),
                    'is_system' => true    
        );           
        
        $columns['arpayment_amount'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Payment Amount'),
                    'type' => 'text',
                    'index' => 'arpayment_amount',
                    'filter' => false,
                    'class' =>'validate-number',
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_PaymentAmount(),
                    'is_system' => true,
                    'width' =>'155px'
        ); 
        
        
      
        
        $checkDisputeActive = Mage::getModel('customerconnect/arpayments')->checkDisputeAllowedOrNot();
        if($checkDisputeActive) {
            $columns['dispute_invoice'] = array(
                        'header' => Mage::helper('epicor_comm')->__('Dispute'),
                        'type' => 'text',
                        'index' => 'dispute_invoice',
                        'filter' => false,
                        'sortable' => false,
                        'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Dispute(),
                        'is_system' => true,
            );    
        }
        
        $columns['aged_period_number']=  array(
            'header' => Mage::helper('epicor_comm')->__('aged_period_number'),
            'index' => 'aged_period_number',
            'filter_index' => 'aged_period_number',
            'condition' => 'EQ',
            'filter_by' =>'linq',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        );           
        
        $columns['details'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Details'),
                    'type' => 'text',
                    'filter' => false,
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Details(),
                    'is_system' => true
        );
        
        $this->setCustomColumns($columns);
    }    
    
    public function getRowClass(Varien_Object $row)
    {
        $outStandingAmount = $row->getOutstandingValue();
        $disableClass      = false;
        if ($outStandingAmount <= 0) {
            $disableClass = true;
        }
        return $disableClass ? 'disable_check_arpayment' : '';
    }
    
    public function getTotals()
    {
        $totals = new Varien_Object();
        $fields = array(
            'outstanding_value' => 0, //actual column index, see _prepareColumns()
            'payment_value' => 0,
            'original_value' => 0,
            'arpayment_amount'=>0
        );
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }
        
        //First column in the grid
        $fields['select_arpayments']='Totals';
        $totals->setData($fields);
        return $totals;
    }    
 
    
    

    
    
    protected function _setCollectionOrder($column)
    {

        $columnId = $this->getRequest()->getParam('agedfilter');
        $values   = explode('_', $columnId);
        if ($this->getCollection() && $column && $values[0] == "aged") {
            $data        = $this->getCollection()->getItems();
            $columnIndex = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
            Mage::register('currentSortColumn', $columnIndex);
            if (strtoupper($column->getDir()) == "ASC") {
                usort($data, function($a, $b)
                {
                    return strcasecmp($a->getData(Mage::registry('currentSortColumn')), $b->getData(Mage::registry('currentSortColumn')));
                });
            } else {
                usort($data, function($a, $b)
                {
                    return strcasecmp($b->getData(Mage::registry('currentSortColumn')), $a->getData(Mage::registry('currentSortColumn')));
                });
            }
            Mage::unregister('currentSortColumn');
            $tmp = new Varien_Data_Collection();
            foreach ($data as $item) {
                $tmp->addItem($item);
            }
            $this->setCollection($tmp);
        } else {
            parent::_setCollectionOrder($column);
        }
        return $this;
    }
    
    public function getGridUrl()
    {
        $columnId = $this->getRequest()->getParam('sort');
        //return $this->getUrl('*/*/grid');
        return $this->getUrl('*/*/grid', array(
            'agedfilter' => $columnId
        ));
    }    
}