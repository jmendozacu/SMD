<?php 
class Silk_EpicorMenu_Block_Customer_Rmas_List_Grid extends Epicor_Customerconnect_Block_Customer_Rmas_List_Grid {

	public function initColumns(){
		$columnConfig = unserialize(Mage::getStoreConfig($this->getMessageBase() . '_enabled_messages/' . strtoupper($this->getMessageType()) . '_request/'.$this->_configLocation));
		$columns = array();
        foreach ($columnConfig as $column) {
            if ($column['filter_by'] == 'none') {
                $column['filter'] = false;
            } else {
                unset($column['filter']);
            }
            if ($column['type'] == 'options' && !empty($column['options'])) {
                $column['options'] = Mage::getModel($column['options'])->toGridArray();
		//$column['filter_condition_callback'] = array($this, '_statusFilter');
            } else if (isset($column['options'])) {
                unset($column['options']);
            }
            
            if($column['type'] == 'number'){
                $column['align'] = 'right';
            }
		if($column['header']!='Status'){
			//$column['filter_condition_callback'] = array($this, '_statusFilter');
			$columns[$column['index']] = $column;
		}
            //$columns[$column['index']] = $column;
        }
        $this->setCustomColumns($columns);

	}
/**
	public function _statusFileter($collection, $column){
		
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}
		
		$this->getCollection()->addFieldToFilter($column['index'], 'Open');
		return $this;
	}
**/
	public function _prepareCollection() {
        $collection = Mage::getModel('epicor_common/message_collection');
        /* @var $collection Epicor_Common_Model_Message_Collection */
        $collection->setMessageBase($this->getMessageBase());
        $collection->setMessageType($this->getMessageType());
        $collection->setIdColumn($this->getIdColumn());
        $collection->setData($this->getCustomData());
        $collection->setDataSubset($this->getDataSubset());
        $collection->setColumns($this->getCustomColumns());
        $collection->setKeepRowObjectType($this->getKeepRowObjectType() ? true : false);
        $collection->setShowAll($this->getShowAll());
        $collection->setGridId($this->getId());
        $collection->setAdditionalFilters($this->getAdditionalFilters());
	//$collection->addFilter('returnsStatus', array('value'=>'Open'));
	$collection->setAdditionalFilters(array('field'=>'returnsStatus', 'value'=>'Open'));
        $collection->setMaxResults($this->getMaxResults());
        if ($this->getCacheDisabled()) {
            $collection->setCacheEnabled(false);
        } else {
            $collection->setCacheEnabled(true);
        }
        $this->setCollection($collection);
        
        //parent::_prepareCollection();
	return Epicor_Common_Block_Generic_List_Grid::_prepareCollection();
        
   	}

}
?>
