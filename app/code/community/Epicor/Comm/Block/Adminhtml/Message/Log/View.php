<?php

class Epicor_Comm_Block_Adminhtml_Message_Log_View extends Mage_Adminhtml_Block_Widget_Container {
   
     private $log = null;
    
    public function __construct()
    {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);
        
        $log = $this->getLog();
        if ($log->getMessageParent()== 'Upload')
        {
            $this->_addButton('resubmit', array(
                'label'     => Mage::helper('adminhtml')->__('Reprocess'),
                'onclick'   => 'setLocation(\'' . $this->getReprocessUrl() . '\')',
                'class'     => 'save',
            ), -1);
        }
        
    }
    
     public function getLog() {
        if (empty($this->log)) {
            $this->log = Mage::registry('message_log_data');
        }
        return $this->log;
    }
    
    public function setHeaderText($str)
    {
        $this->_headerText=$str;
    }
    
    public function getBackUrl()
    {
         $source= Mage::registry('message_log_source');
         $param = Mage::registry('message_log_sourceparam');
        return $this->getUrl($source,$param);
    }
    
    
    public function getReprocessUrl()
    {
        $log=$this->getLog();
        return  $this->getUrl('*/*/reprocess', array('id'=>$log->getId()));
    }

}