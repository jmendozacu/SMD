<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Block_Slider_Edit_Tab_Sections extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sliderblocksectionsgrid');
        $this->setDefaultSort('section_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);


        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('flexitheme/layout_block')->getCollection();
        /* @var $collection Epicor_FlexiTheme_Model_Mysql4_Layout_Block_Collection */
        $collection->addFilter('block_flexi_type', 'callout_block');
        
        foreach ($collection as $key => $slider_section)
        {
            $data = unserialize($slider_section->getBlockExtra());
            $slider_section->setSectionType($data['type']);
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('in_group', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'field_name' => 'sections[]',
            'name' => 'customer',
            'values' => array_values($this->getSelectedSections()),
            'align' => 'center',
            'editable' => true,
            'index' => 'entity_id'
        ));

        $this->addColumn('section_name', array(
            'header' => Mage::helper('flexitheme')->__('Callout Name'),
            'align' => 'left',
            'index' => 'block_name',
        ));


        $this->addColumn('section_type', array(
            'header' => Mage::helper('flexitheme')->__('Callout Type'),
            'align' => 'left',
            'index' => 'section_type',
        ));


//        $onclick = 'submitAndReloadArea(form_tabs_form_section_content,$(this).readAttribute(\'href\'));return false;';
//
//        $this->addColumn('action', array(
//            'header' => Mage::helper('flexitheme')->__('Action'),
//            'width' => '100',
//            'type' => 'action',
//            'getter' => 'getId',
//            'actions' => array(
//                array(
//                    'caption' => Mage::helper('flexitheme')->__('Edit'),
//                    'field' => 'id',
//                    'onclick' => $onclick
//                )
//            ),
//            'filter' => false,
//            'sortable' => false,
//            'index' => 'stores',
//            'is_system' => true,
//        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return null; //$this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getSelectedSections()
    {
        $model = Mage::registry('layout_block_data');
        $data = unserialize($model->getBlockExtra());
        return $data['sections'];
    }

}