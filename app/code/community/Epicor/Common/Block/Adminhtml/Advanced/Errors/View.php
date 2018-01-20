<?php

/**
 * Error report view
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Errors_View extends Mage_Adminhtml_Block_Widget_Container {

    private $_fileDir = '';
    private $_fileName = false;

    public function __construct() {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('widget/form/container.phtml');
        }

        $this->_fileDir = $logFilePath = Mage::getBaseDir('var') . DS . 'report' . DS;
        $this->_fileName = Mage::registry('report_filename');
        $this->_headerText = 'Viewing report ' . $this->_fileName;

        $this->_addButton('back', array(
            'label' => Mage::helper('adminhtml')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*/index') . '\')',
            'class' => 'back',
                ), -1);
    }

    public function getFilepath() {
        return $this->_fileDir . $this->_fileName;
    }

    public function getFilename() {
        return $this->_fileName;
    }

    public function getFiledate() {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $time = date('Y-m-d H:i:s', filemtime($this->_fileDir . $this->_fileName));
        return $helper->formatDate($time, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
    }

}
