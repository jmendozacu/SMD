<?php

class Epicor_FlexiTheme_Block_Adminhtml_Translation_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('translationdisplaygrid');
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

    protected function _prepareColumns() {
       
        $this->addColumn('translation_language', array(
            'header' => Mage::helper('flexitheme')->__('Language'),
            'align' => 'left',
            'index' => 'translation_language',
        ));

        $this->addColumn('language_code', array(
            'header' => Mage::helper('flexitheme')->__('Language Code'),
            'align' => 'left',
            'index' => 'language_code',
        ));

        $this->addColumn('active', array(
            'header' => Mage::helper('flexitheme')->__('Status'),
            'align' => 'left',
            'index' => 'active',
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('flexitheme')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('flexitheme')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('flexitheme')->__('Delete'),
                    'url' => array('base' => '*/*/deleteLanguage'),
                    'field' => 'id',
                    'confirm' => Mage::helper('flexitheme')->__('Are you sure you want to delete this language? This action cannot be undone')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}

