<?php

class Epicor_Comm_Block_Adminhtml_Advanced_Entity_Register_Grid
        extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        $this->setId('entity_register_grid');
        $this->setDefaultSort('row_id');
        $this->setDefaultDir('desc');
        
        $this->setDefaultFilter(
            array(
                'is_dirty' => 1,
                'type' => Mage::getStoreConfig('epicor_comm_enabled_messages/syn_request/uploaded_data_filter')
            )
        );
        
        $this->setSaveParametersInSession(false);
        parent::__construct();
        
    }

    protected function _prepareCollection()
    {
        $this->setSaveParametersInSession(false);
        $collection = Mage::getModel('epicor_comm/entity_register')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {

        $this->addColumn(
            'type', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Type'),
                'align' => 'left',
                'index' => 'type',
                'renderer' => new Epicor_Comm_Block_Adminhtml_Advanced_Entity_Register_Renderer_Type(),
                'width' => '200px',
                'filter_condition_callback' => array($this, 'filterType')
            )
        );

        $this->addColumn(
            'details', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Entity Details'),
                'align' => 'left',
                'index' => 'details',
                'width' => '700px'
            )
        );

        $this->addColumn(
            'is_dirty', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Mismatch'),
                'align' => 'left',
                'type' => 'options',
                'options' => array(
                    '1' => 'Yes',
                    '0' => 'No'
                ),
                'index' => 'is_dirty',
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
            'modified_at', 
            array(
                'header' => Mage::helper('epicor_comm')->__('Modified At'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'modified_at',
                'filter_time' => true
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
                $types[] = $type;
            }
        }
        
        $filterValue = explode(',', $filterValue);
        
        foreach ($filterValue as $value) {
            foreach ($typeDescs as $type => $desc) {
                if (strpos(strtolower($desc), $value) !== false 
                    || strpos(strtolower($type), $value) !== false) {
                    $types[] = $type;
                }
            }
        }
        
        $inNin = $helper->getRegistryTypes($types);

        $collection->addFieldToFilter($column->getId(), array('in' => array_unique($inNin)));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('row_id');
        $this->getMassactionBlock()->setFormFieldName('rowid');

        $this->getMassactionBlock()->addItem(
            'mark_for_deletion', 
            array(
                'label' => Mage::helper('epicor_comm')->__('Mark For Deletion'),
                'url' => $this->getUrl('*/*/markForDeletion'),
                'confirm' => Mage::helper('epicor_comm')->__('Delete selected items?')
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

}
