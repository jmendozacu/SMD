<?php

class Epicor_FlexiTheme_Block_Adminhtml_Usertranslation_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('usertranslationdisplaygrid');
        $this->setDefaultSort('translation_language');
        //    $this->setDefaultDir('ASC');
        $this->setDefaultDir('DSC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('flexitheme/translation_language')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
//        $this->addColumn('language_id', array(
//            'header'    => Mage::helper('flexitheme')->__('Language'),
//            'align'     =>'left',
//            'name'     => 'language',
//            'column_css_class'=> 'no-display',
//            'header_css_class'=> 'no-display',
//            'renderer'  => 'flexitheme/adminhtml_usertranslation_edit_renderer_language',
//        ));
//        $this->addColumn('rowdata', array(
//            'header'    => Mage::helper('flexitheme')->__('Row'),
//            'align'     =>'left',
//            'name'     => 'rowdata',            
//            'renderer'  => 'flexitheme/adminhtml_usertranslation_edit_renderer_userTranslationRowdata',
//            'column_css_class'=> 'no-display',
//            'header_css_class'=> 'no-display',
//        ));
        $this->addColumn('translation_string', array(
            'header'    => Mage::helper('flexitheme')->__('User Specified Phrase'),
            'align'     =>'left',
            'name'     => 'translation_string',            
            'index'     => 'translation_string',
            'filter_index' => 'translation_string',
            'class'     => 'required-entry',
        ));
        
//
//        $this->addColumn('translated_phrase', array(
//            'header'    => Mage::helper('flexitheme')->__('Translated Phrase'),
//            'align'     =>'left',
//            'name'     => 'translated_phrase',
//            'index'     => 'translated_phrase',
//            'filter_index' => 'translated_phrase',
//            'class'     => 'required-entry',
//            'type'      => 'input',
//        ));
//
//      below to be readded when autotranslate complete 
         $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('flexitheme')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getRowdata',
               
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('flexitheme')->__('Delete'),                        
                        'url'       => array('base'=> '*/*/deleteUsertranslation'),
                        'field'     => 'rowdata'
                    ),                   
                    array(
                        'caption'   => Mage::helper('flexitheme')->__('Add User Usertranslation'),                        
                        'url'       => array('base'=> '*/*/addUsertranslation'),
                        'field'     => 'rowdata'
                    ),                   
                ),
                
//                'filter'    => false,
                'sortable'  => false, 
//                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}



