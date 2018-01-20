<?php

/**
 * System log view block
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Syslog_View extends Mage_Adminhtml_Block_Widget_Container {

    private $_logFileDir = '';
    private $_logFileName = false;

    public function __construct() {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }

        $this->_logFileDir = $logFilePath = Mage::getBaseDir('var') . DS . 'log' . DS;
        $this->_logFileName = Mage::registry('log_filename');
        $this->_headerText = 'Viewing ' . $this->_logFileName;

        $this->_addButton('back', array(
            'label' => Mage::helper('adminhtml')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
            'class' => 'back',
                ), -1);
    }

    public function getFilepath() {
        return $this->_logFileDir . $this->_logFileName;
    }

    public function getFilename() {
        return $this->_logFileName;
    }

    public function getFiledate() {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $time = date('Y-m-d H:i:s', filemtime($this->_logFileDir . $this->_logFileName));
        return $helper->formatDate($time, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
    }

}
