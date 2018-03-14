<?php

/**
 * system log grid 
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Syslog_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('advancedSyslogGrid');
        $this->setDefaultSort('last_modified');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);

        $this->setMessageBase('epicor_common');
        $this->setIdColumn('id');

        $this->setCacheDisabled(true);

        $this->setCustomData($this->getCustomData());
    }

    protected function getCustomData() {
        $logFileDir = Mage::getBaseDir('var') . DS . 'log' . DS;

        $files = array();

        if (file_exists($logFileDir)) {
            $logFiles = scandir($logFileDir);

            foreach ($logFiles as $file) {
                if (!in_array($file, array('.', '..'))) {
                    $files[] = new Varien_Object(array(
                        'filename' => $file,
                        'size' => filesize($logFileDir . $file),
                        'last_modified' => date('Y-m-d H:i:s', filemtime($logFileDir . $file)),
                        'action' => $this->_checkSrcIsFile($logFileDir . $file),
                    ));
                }
            }
        }

        return $files;
    }
    
    
    protected function _checkSrcIsFile($src)
    {
        $result = false;
        if (!is_readable($src)) {
            $result['status'] = "notreadable";
        } else if(!is_writable($src)) {
            $result['status'] = "notwritable";
        }
        return $result;
     }    

    protected function _getColumns() {
        $columns = array();

        $columns['filename'] = array(
            'header' => Mage::helper('flexitheme')->__('Name'),
            'align' => 'left',
            'index' => 'filename',
            'type' => 'text',
            'condition' => 'LIKE'
        );

        $columns['size'] = array(
            'header' => Mage::helper('flexitheme')->__('Size'),
            'align' => 'left',
            'index' => 'size',
            'type' => 'number',
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Filesize(),
            'filter' => false
        );

        $columns['last_modified'] = array(
            'header' => Mage::helper('flexitheme')->__('Last Modified'),
            'align' => 'left',
            'index' => 'last_modified',
            'type' => 'datetime',
        );
        
        $columns['action'] = array(
            'header' => Mage::helper('flexitheme')->__('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'text',
            'filter' => false,
            'renderer' => new Epicor_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Filepermission(),
            'sortable'=>false
        );        

        return $columns;
    }

    public function getRowUrl($row) {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $log = $helper->urlEncode($helper->encrypt($row->getFilename()));
        //return $this->getUrl('*/*/view', array('log' => $log));
    }

}
