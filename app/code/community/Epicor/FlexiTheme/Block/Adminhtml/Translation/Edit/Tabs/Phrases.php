<?php
#class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Tabs_Phrases extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
class Epicor_Flexitheme_Block_Adminhtml_Translation_Edit_Tabs_Phrases extends Mage_Adminhtml_Block_Widget_Grid 

{
    protected $_gridPhrases;
    protected $_language;
    protected $_languageCode;
    public function __construct()
    {
          
        parent::__construct();
        $this->setId('phraseGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
   #     $this->setRowClickCallback('translationPhrasesUpdateGrid.phrasesGridRowClick.bind(translationPhrasesUpdateGrid)');
   #     $this->setCheckboxCheckCallback('translationPhrasesUpdateGrid.phrasesGridCheckboxCheck.bind(translationPhrasesUpdateGrid)');
        $this->setRowInitCallback('translationPhrasesUpdateGrid.phrasesGridRowInit.bind(translationPhrasesUpdateGrid)');
        $this->setUseAjax(true);
     
    }
     public function canShowTab() {
        return true;
    }

    public function getTabLabel() {
        return 'Display Phrases';
    }

    public function getTabTitle() {
        return 'Display Phrases';
    }
     public function isHidden() {
        return false;
    }
    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('Site Text Editor - Display Site Text Phrases');
    }
    protected $optionsArray = Array();  
 

    protected function _prepareCollection()
    {  
       $this->_language = Mage::registry('translation_language_data'); 
       if($this->_language){ 
            $this->setCollection($this->_language->getPhrases());
       } 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('language_id', array(
            'header'    => Mage::helper('flexitheme')->__('Language'),
            'align'     =>'left',
            'name'     => 'language',
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',
            'renderer'  => 'flexitheme/adminhtml_translation_edit_renderer_language',
        ));
        $this->addColumn('rowdata', array(
            'header'    => Mage::helper('flexitheme')->__('Row'),
            'align'     =>'left',
            'name'     => 'rowdata',            
            'renderer'  => 'flexitheme/adminhtml_translation_edit_renderer_rowdata',
            'column_css_class'=> 'no-display',
            'header_css_class'=> 'no-display',
        ));
        $this->addColumn('translation_string', array(
            'header'    => Mage::helper('flexitheme')->__('Phrase'),
            'align'     =>'left',
            'name'     => 'translation_string',            
            'index'     => 'translation_string',
    #        'filter_index' => 'translation_string',
        ));
        

        $this->addColumn('translated_phrase', array(
            'header'    => Mage::helper('flexitheme')->__('Translated Phrase'),
            'align'     =>'left',
            'name'     => 'translated_phrases',
            'index'     => 'translated_phrase',
            'renderer'  => 'flexitheme/adminhtml_translation_edit_renderer_phrases',            
            'phrases'   =>  $this->_getSelectedPhrases(),
            'type'      => 'input',
     #       'filter_index' => 'translated_phrase',
        ));
//
//         $this->addColumn('translation_id[]', array(
//            'header'    => Mage::helper('flexitheme')->__('Phrase Id'),
//            'align'     =>'left',
//            'style'     => 'visibility:hidden',
//            'index'     => 'language_id',
//            'type'      => 'options',
//            'options' =>  $this->optionsArray,
//        ));
//        $this->addColumn('translation_language', array(
//            'header'    => Mage::helper('flexitheme')->__('Translation Language'),
//            'align'     =>'left',
//            'index'     => 'language_id',
//            'type'      => 'options',
//            'options' =>  $this->optionsArray,
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
                        'caption'   => Mage::helper('flexitheme')->__('Auto Translate'),
                        'url'       => array('base'=> '*/*/singleTranslate'
                                                ),
                        'field'     => 'rowdata'
                    ),                   
                ),
                'filter'    => false,
                'sortable'  => false, 
//                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {   
    }
    
    protected function _getSelectedPhrases()
    {
        if(!$this->_gridPhrases){
            $gridPhrases = $this->getRequest()->getPost('gridPhrases', array());
            if(!is_array($gridPhrases)){
                $gridPhrases = json_decode($gridPhrases, true);
            }
            
            $this->_gridPhrases = new Varien_Object($gridPhrases);
        }    
      
        return $this->_gridPhrases;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/nextPagePhrases', array('_current'=>true));
    } 
}

