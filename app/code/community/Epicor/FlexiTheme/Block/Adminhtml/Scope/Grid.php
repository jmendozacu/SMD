<?php

class Epicor_FlexiTheme_Block_Adminhtml_Scope_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    protected $_layoutOrSkinName;
    protected $_layoutOrSkin;
    public function __construct() {
        parent::__construct();
        $this->setId('flexithemescopegrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setDefaultDir('DSC');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->_layoutOrSkinName = Mage::registry('layoutOrSkinName');
        $this->_layoutOrSkin = Mage::registry('action_type'); 
    }

    protected function _prepareCollection() {
        $layoutNameSafeString = Mage::helper('flexitheme')->safeString(Mage::registry('layoutOrSkinName'));
        $collection = Mage::getModel('core/config_data')->getCollection()
		#      ->addFieldToFilter('path',array('like'=>'design/package/name')) 
                      ->addFieldToFilter('path',array('like'=>"design/theme/{$this->_layoutOrSkin}")) 
		      ->addFieldToFilter('scope',array('eq'=>'stores'));                        
        
        // determine if default scope is set to layoutOrSkinName
        $defaultColl = Mage::getModel('core/config_data')->getCollection()
              #      ->addFieldToFilter('path',array('like'=>'design/package/name')) 
                    ->addFieldToFilter('path',array('like'=>"design/theme/{$this->_layoutOrSkin}")) 
                    ->addFieldToFilter('scope',array('eq'=>'default'));  
        $default = $defaultColl->getData();  
        $default = trim($default[0]['value']);
        
        
        $collArray = array();
        foreach($collection as $coll){
              
            $id = $coll->getScopeId();
            
                // save store if it is already set to required skin
                if (trim($coll->getValue()) == $layoutNameSafeString){
                    $collArray[$coll->getScopeId()] = trim($coll->getValue());
                }
        }
        foreach (Mage::app()->getWebsites() as $website) {           
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores(); 
                foreach ($stores as $store) {                    
                    $storeArray[$store->getId()] = array(  
                                                    'store_id'=>$store->getId(), 
                                                    'store_name'=>$store->getName(), 
                                                    'website_id' => $website->getId(),
                                                    'website_name' =>$website->getName(),       
                                                    'active' =>false       
                            );
                    
                    //if current store is already on the table for this skin/layout, display as active
                    if (array_key_exists($store->getId(), $collArray)) {
                        if ($collArray[$store->getId()] == $layoutNameSafeString){
                            $storeArray[$store->getId()]['active'] = true;
                        }
                    } else {
                        // if the default set to layout/skin name, 
                        // and there is no other entry for for the store, the store will be active
                        if ($default == $layoutNameSafeString) {
                            $storeColl = Mage::getModel('core/config_data')->getCollection()
                              ->addFieldToFilter('path',array('like'=>"design/theme/{$this->_layoutOrSkin}")) 
                              ->addFieldToFilter('scope_id',array('eq'=>$store->getId()));
                            if(!$storeColl->getData()){  
                                $storeArray[$store->getId()]['active'] = true;
                            }    
                        }
                    }
                }
            }
        }
        $collection = new Varien_Data_Collection();
        
        $varienStoreArray = new Varien_Object($storeArray);
        foreach($varienStoreArray->getData() as $storeList){            
            $storeData = new Varien_Object($storeList);
            $storeData->setIdFieldName('store_id');
            $collection->addItem($storeData);           
        } 
   
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
     
        $this->addColumn('skinOrLayout_active', array(
            'header' => $this->_layoutOrSkinName.Mage::helper('flexitheme')->__(' Active'),
            'align' => 'left',
            'index' => 'active',
            'type'    => 'options',
            'options' => array(0 => $this->__('No'), 1 => $this->__('Yes'))
        ));
        $this->addColumn('Store', array(
            'header' => Mage::helper('flexitheme')->__('Store'),
            'align' => 'left',
            'index' => 'store_name',
  #          'renderer' =>  new Epicor_Flexitheme_Block_Adminhtml_Renderer_Store(),
        ));
       
        $this->addColumn('Store Id', array(
            'header' => Mage::helper('flexitheme')->__('Store Id'),
            'align' => 'left',
            'index' => 'store_id',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display',         
        ));
        
        $this->addColumn('Website', array(
            'header' => Mage::helper('flexitheme')->__('Website'),
            'align' => 'left',
            'index' => 'website_name',
        ));
        $this->addColumn('Website Id', array(
            'header' => Mage::helper('flexitheme')->__('Website Id'),
            'align' => 'left',
            'index' => 'website_id',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display',          
        ));
                             

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
   #     return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        return null;
    }
    protected function _prepareMassaction()
    {
    #    parent::_prepareMassaction();
        $this->setMassactionIdField('store_id');
        $this->getMassactionBlock()->setFormFieldName('scope_checkbox');
        $this->getMassactionBlock()->addItem('activate', array(
        'label'=> Mage::helper('flexitheme')->__('Activate'),
        'url'  => $this->getUrl('*/*/activateSelectedStores', array('layoutOrSkin' =>$this->_layoutOrSkinName,'actionType'=>$this->_layoutOrSkin))
            ));
        $this->getMassactionBlock()->addItem('deactivate', array(
        'label'=> Mage::helper('flexitheme')->__('Dectivate'),           
        'url'  => $this->getUrl('*/*/deactivateSelectedStores', array('layoutOrSkin' =>$this->_layoutOrSkinName,'actionType'=>$this->_layoutOrSkin)),        
        ));
        return $this;
    }
     
}

