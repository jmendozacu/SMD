<?php

class Epicor_FlexiTheme_Block_Adminhtml_Layout_Template_Edit_Tab_Sections extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('layouttemplatesectionsgrid');
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
      
        if(Mage::getSingleton('adminhtml/session')->getTempTemplateId())
            $template_id = Mage::getSingleton('adminhtml/session')->getTempTemplateId();
        elseif(Mage::registry('layout_template_data'))
            $template_id = Mage::registry('layout_template_data')->getId();
        
        $collection = Mage::getModel('flexitheme/layout_template_section')->getCollection()
              ->addFilter('template_id', $template_id)->load();
      $new_items = Mage::getSingleton('adminhtml/session')->getNewTemplateSections();
            
      foreach ($new_items as $section){
          if($section->getId() < 0)
            $collection->addItem($section);
          else {
              foreach($collection->getItems() as $key => $item) {
                  if($item->getId() == $section->getId()) {
                      $collection->removeItemByKey($key);
                      break;
                  }
              }
          }
      }
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      
      $this->addColumn('section_name', array(
          'header'    => Mage::helper('flexitheme')->__('Section Name'),
          'align'     =>'left',
          'index'     => 'section_name',
      ));

      
        $onclick = 'submitAndReloadArea(form_tabs_form_section_content,$(this).readAttribute(\'href\'));return false;';

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('flexitheme')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('flexitheme')->__('Delete'),
                        'url'       => array('base'=> '*/*/deletesection'),
                        'field'     => 'id',
                        'onclick'   => $onclick
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	  
      return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return null;//$this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
}