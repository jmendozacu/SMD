<?php

class Epicor_Flexitheme_Block_Adminhtml_Usertranslation_Edit_Tabs_Usertranslations extends Mage_Adminhtml_Block_Widget_Grid 
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_gridPhrases;
    protected $_language;
    protected $_languageCode;
    public function __construct()
    {
        parent::__construct();
        $this->setId('userUsertranslationGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
   #     $this->setRowClickCallback('usertranslationPhrasesUpdateGrid.phrasesGridRowClick.bind(usertranslationPhrasesUpdateGrid)');
   #     $this->setCheckboxCheckCallback('usertranslationPhrasesUpdateGrid.phrasesGridCheckboxCheck.bind(usertranslationPhrasesUpdateGrid)');
   #     $this->setRowInitCallback('usertranslationPhrasesUpdateGrid.phrasesGridRowInit.bind(usertranslationPhrasesUpdateGrid)');
        $this->setUseAjax(true);  
//        $this->setFilterVisibility(false);
    }
     public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Usertranslations';
    }

    public function getTabTitle() {
        return 'Usertranslations';
    }
     public function isHidden() {
        return false;
    }
    public function getHeaderText()
    {
//        return Mage::helper('adminhtml')->__('Site Text Editor - Display Site Text Phrases');
    }
    protected $optionsArray = Array();  
 
    protected function _prepareCollection()
    {  
        $this->setCollection(Mage::getModel('flexitheme/translation_data')->getUserDefinedPhrases());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('translation_string', array(
            'header'    => Mage::helper('flexitheme')->__('User Specified Phrase'),
            'align'     =>'left',
            'name'     => 'translation_string',            
            'index'     => 'translation_string',
            'filter_index' => 'translation_string',
            'width'     => '90%',
            'class'     => 'required-entry',
        ));
        
//      below to be readded when autotranslate complete 
         $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('flexitheme')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
               
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('flexitheme')->__('Delete'),                        
                        'url'       => array('base'=> '*/*/deleteBaseUsertranslationEntry'),
                        'field'     => 'id'
                    ),                   
                ),
                
//                'filter'    => false,
                'sortable'  => false, 
//                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html.= $this->getAddButtonHtml();
        return $html;
    }
    
      public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getRowUrl($row)
    {   
    }
public function getGridUrl()
    {    
        return $this->getUrl('*/*/nextPageUserTranslations', array('_current'=>true));    
    } 
}

