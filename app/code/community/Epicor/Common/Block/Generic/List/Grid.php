<?php

/**
 * Generic grid list for use with  messages
 * 
 * @method setMessageBase()
 * @method getMessageBase()
 * @method setCacheDisabled()
 * @method getCacheDisabled()
 * @method setMessageType()
 * @method getMessageType()
 * @method setDataSubset()
 * @method getDataSubset()
 * @method getIdColumn()
 * @method setIdColumn()
 * @method setRowUrlValue()
 * @method getRowUrlValue()
 * @method getCustomData()
 * @method setCustomData()
 * @method getShowAll()
 * @method setShowAll()
 * @method getAdditionalFilters()
 * @method setAdditionalFilters()
 * @method setMaxResults()
 * @method getMaxResults()
 * @method boolean getKeepRowObjectType()
 * @method setKeepRowObjectType(boolean $keepObject)
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Generic_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {

        parent::__construct();
        if (!$this->getRowValue()) {
            $this->setRowUrlValue('*/*/edit');
        }

        // Default setup example
        // $this->setId('id');
        // $this->setDefaultSort('start_datestamp');
        // $this->setDefaultDir('desc');
        // $this->setSaveParametersInSession(true);
        //
        // $this->setCustomColumns(array());
        //
        // $this->setExportTypeCsv(array(
        //     'url' => '*/*/exportCsv',
        //     'text' => 'CSV'
        // ));
        //
        // $this->setExportTypeXml(array(
        //     'url' => '*/*/exportXml',
        //     'text' => 'XML'
        // ));
        //
        // $this->setRowUrlValue('*/*/view');
        
    }

    protected function _prepareCollection() {
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
        $collection->setMaxResults($this->getMaxResults());
        if ($this->getCacheDisabled()) {
            $collection->setCacheEnabled(false);
        } else {
            $collection->setCacheEnabled(true);
        }

        $this->setCollection($collection);
        
        parent::_prepareCollection();
        
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        if (!$this->getCacheDisabled()) {
            $hideRefeshMsg = $this->getHideRefreshMessage();
            $internalFilter =  $this->getInternalFilter();            
            if(!$internalFilter) {
                $cacheTime = $this->getCollection()->getCacheTime();
            } else {
                $cacheTime = now();
            }
            
            if($this->getRequest()->getParam('ajax')) {
               $hideRefeshMsg = true; 
            }            

            $date = Mage::helper('epicor_common')->getLocalDate($cacheTime);

            $identifier = $this->getMessageType() ? : $this->getId();

            $url = $this->getUrl('*/grid/clear', array('grid' => $identifier, 'location' => Mage::helper('core/url')->getEncodedUrl()));
            if(!$hideRefeshMsg) {
                $html = '<p>'.$this->__('Data correct as of %s', $date) . ' <a href="' . $url . '">'.$this->__('Refresh Data') . '</a></p>' . $html;
            }
        }
        return $html;
    }

    protected function _prepareColumns()
    {

        foreach ($this->getCustomColumns() as $columnId => $columnInfo) {
            if (isset($columnInfo['type']) && $columnInfo['type'] == 'date'){
                $columnInfo['renderer'] = 'Epicor_Customerconnect_Block_List_Renderer_Date';
            }
            
            $columnInfo['header'] = $this->__(isset($columnInfo['header']) ? $columnInfo['header'] : '');
            $this->addColumn($columnId, $columnInfo);
        }

        $exportCsv = $this->getExportTypeCsv();
        if ($exportCsv) {
            $this->addExportType(@$exportCsv['url'], Mage::helper('epicor_common')->__(@$exportCsv['text']));
        }

        $exportXml = $this->getExportTypeXml();
        if ($exportXml) {
            $this->addExportType(@$exportXml['url'], Mage::helper('epicor_common')->__(@$exportXml['text']));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl($this->getRowUrlValue(), array('id' => $row->getId()));
    }
    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                $column->getFilter()->setValue($data[$columnId]);
                
                $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }
    /**
     * Retrieve grid
     *
     * @param   string $paramName
     * @param   mixed $default
     * @return  mixed
     */
    public function getParam($paramName, $default=null)
    {
        if ($this->getRequest()->has($paramName)) {
            $param = $this->getRequest()->getParam($paramName);            
            return $param;
        } 
        return $default;
    }

    public function getVisibleColumns()
    {
        $visibleColumns = 0;
        foreach ($this->getColumns() as $column) {
            if ($column->getType() != 'hidden')
                $visibleColumns++;
        }
        
        return $visibleColumns;
    }

}
