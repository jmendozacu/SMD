<?php

/*
 * epicor_comm_erp_mapping attributes grid
 */

class Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /*
     * construct for epicor_comm_erp_mapping attributes grid
     */

    public function __construct() {
        parent::__construct();
        $this->setId('erpattributesmappingGrid');
        $this->setDefaultSort('erp_code');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /*
     * Setup collection for epicor_comm_erp_mapping attributes grid
     */

    protected function _prepareCollection() {
        $collection = Mage::getModel('epicor_comm/erp_mapping_attributes')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /*
     * Setup columns for epicor_comm_erp_mapping attributes grid
     */

    protected function _prepareColumns() {
        $yesno = Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray();
        $this->addColumn(
                'attribute_code', array(
            'header' => Mage::helper('epicor_comm')->__('Attribute Code'),
            'align' => 'left',
            'index' => 'attribute_code',
                )
        );
        $this->addColumn(
                'input_type', array(
            'header' => Mage::helper('epicor_comm')->__('Input Type'),
            'align' => 'left',
            'index' => 'input_type',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Renderer_Inputtype()                
                )
        );
        $this->addColumn(
                'separator', array(
            'header' => Mage::helper('epicor_comm')->__('Separator'),
            'align' => 'left',
            'index' => 'separator',
                )
        );
        $this->addColumn(
                'use_for_config', array(
            'header' => Mage::helper('epicor_comm')->__('Use For Config'),
            'align' => 'left',
            'index' => 'use_for_config',
            'type'=>'options',                   
            'options' => $yesno,        
                )
        );
        $this->addColumn(
                'quick_search', array(
            'header' => Mage::helper('epicor_comm')->__('Quick Search'),
            'align' => 'left',
            'index' => 'quick_search',
            'type'=>'options',                   
            'options' => $yesno,             
                )
        );
        $this->addColumn(
                'advanced_search', array(
            'header' => Mage::helper('epicor_comm')->__('Advanced Search'),
            'align' => 'left',
            'index' => 'advanced_search',
            'type'=>'options',                   
            'options' => $yesno,             
                )
        );
        $this->addColumn(
                'search_weighting', array(
            'header' => Mage::helper('epicor_comm')->__('Search Weighting'),
            'align' => 'left',
            'index' => 'search_weighting',                   
                )
        );


        $this->addColumn(
                'use_in_layered_navigation', array(
            'header' => Mage::helper('epicor_comm')->__('Use In Layered Navigation'),
            'align' => 'left',
            'index' => 'use_in_layered_navigation',
            'renderer' => new Epicor_Comm_Block_Adminhtml_Mapping_Erpattributes_Renderer_Useinlayerednavigation()        
                )
        );
        $this->addColumn(
                'search_results', array(
            'header' => Mage::helper('epicor_comm')->__('Search Results'),
            'align' => 'left',
            'index' => 'search_results',
            'type'=>'options',                   
            'options' => $yesno,
                )
        );
        $this->addColumn(
                'visible_on_product_view', array(
            'header' => Mage::helper('epicor_comm')->__('Visible On Product View'),
            'align' => 'left',
            'index' => 'visible_on_product_view',
            'type'=>'options',                   
            'options' => $yesno,                
                )
        );

        $this->addColumn(
                'action', array(
            'header' => Mage::helper('epicor_comm')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('epicor_comm')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => Mage::helper('epicor_comm')->__('Delete selected items?')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
                )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('epicor_comm')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('epicor_comm')->__('XML'));

        return parent::_prepareColumns();
    }

    /*
     * Allow click on row to be editable for epicor_comm_erp_mapping attributes grid
     */

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
