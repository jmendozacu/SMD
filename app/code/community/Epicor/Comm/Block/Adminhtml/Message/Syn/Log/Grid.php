<?php

class Epicor_Comm_Block_Adminhtml_Message_Syn_Log_Grid
        extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('syn_log_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        
//        $this->setDefaultFilter(
//            array('is_auto' => '')
//        );
        
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('epicor_comm/syn_log')->getCollection();
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn(
            'entity_id', 
            array(
                'header' => Mage::helper('epicor_comm')->__('ID'),
                'align' => 'left',
                'index' => 'entity_id',
                'type' => 'range',
                'width' => '50'
            )
        );

        $this->addColumn(
            'types', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Types'),
                'align' => 'left',
                'index' => 'types',
                'renderer' => new Epicor_Comm_Block_Adminhtml_Message_Syn_Log_Renderer_Types(),
                'filter_condition_callback' => array($this, 'filterType')
            )
        );

        $this->addColumn(
            'created_at', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Created At'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'created_at',
            )
        );

        $this->addColumn(
            'is_auto',
            array(
                'header' => Mage::helper('epicor_comm')->__('Is Auto Sync?'),
                'align' => 'center',
                'index' => 'is_auto',
                'width' => '75px',
                'type' => 'options',
                'options' => array(
                    '1' => 'Auto',
                    '0' => 'Manual'
                ),
                'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
            )
        );
        
        $this->addColumn(
            'full_sync',
            array(
                'header' => Mage::helper('epicor_comm')->__('Full Sync?'),
                'align' => 'center',
                'index' => 'from_date',
                'width' => '75px',
                'type' => 'options',
                'options' => array(
                    'yes' => 'Yes',
                    'no' => 'No'
                ),
                'tick_mode' => 'empty',
                'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Tickcross(),
                'filter_condition_callback' => array($this, 'filterIsFull')
            )
        );
        
        $this->addColumn(
            'uploaded', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Uploaded Data'),
                'align' => 'left',
                'index' => 'uploaded',
                'filterable' => false,
                'sortable'=> false,
                'renderer' => new Epicor_Comm_Block_Adminhtml_Message_Syn_Log_Renderer_Uploadedlink(),
            )
        );

        return parent::_prepareColumns();
    }
    
    /**
     * Filters the is full sync column
     * 
     * @param Epicor_Common_Model_Message_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function filterType($collection, $column)
    {
        $filterValue = strtolower($column->getFilter()->getValue());
        
        $helper = Mage::helper('epicor_comm/entityreg');
        /* @var $helper Epicor_Comm_Helper_Entityreg */
        
        $typeDescs = $helper->getRegistryTypeDescriptions();
        
        $types = array();
        
        foreach ($typeDescs as $type => $desc) {
            if (strpos($filterValue, strtolower($desc)) !== false 
                || strpos($filterValue, strtolower($type)) !== false) {
                $types[] = '%'.$type.'%';
            }
        }
        
        $filterValue = explode(',', $filterValue);
        
        foreach ($filterValue as $value) {
            foreach ($typeDescs as $type => $desc) {
                if (strpos(strtolower($desc), $value) !== false 
                    || strpos(strtolower($type), $value) !== false) {
                    $types[] = '%'.$type.'%';
                }
            }
        }
        
        $filter = array();
        
        foreach ($types as $type) {
            $filter[] = array('like' => $type);
        }
        
        if (!empty($filter)) {
            $collection->addFieldToFilter('types', $filter);
        } else {
            $collection->addFieldToFilter('types', $filterValue);
        }
    }
    
    /**
     * Filters the is full sync column
     * 
     * @param Epicor_Common_Model_Message_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function filterIsFull($collection, $column)
    {
        $filterValue = $column->getFilter()->getValue();
        if ($filterValue == 'yes') {
            $collection->addFieldToFilter('from_date', array('null' => true));
        } else {
            $collection->addFieldToFilter('from_date', array('null' => false));
        }
    }
    
    /**
     * Filters the is manual/auto column
     * 
     * @param Epicor_Common_Model_Message_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function filterManualAuto($collection, $column)
    {
        $filterValue = $column->getFilter()->getValue();
        if ($filterValue == 'yes') {
            $collection->addFieldToFilter('is_auto', array('eq' => '1'));
        } else {
            $collection->addFieldToFilter('is_auto', array('eq' => '0'));
        }
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('logid');

        $this->getMassactionBlock()->addItem(
            'delete', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Delete'),
                'url' => $this->getUrl('*/*/deletelog'),
                'confirm' => Mage::helper('epicor_comm')->__('Delete selected SYN log entries?')
            )
        );

        return $this;
    }
    
    public function getRowUrl($row)
    {
        return false;
    }

}
