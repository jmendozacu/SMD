<?php

/**
 * Nginx log grid 
 * 
 * @category   Epicor
 * @package    Epicor_HostingManager
 * @author     Epicor Websales Team
 */
class Epicor_HostingManager_Block_Adminhtml_Nginxlog_Grid extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('nginxlogGrid');
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

    protected function getCustomData()
    {
        $logFileDir = DS . 'var' . DS . 'log' . DS . 'nginx' . DS;

        $files = array();
        if (file_exists($logFileDir)) {
            $logFiles = scandir($logFileDir);
            $file_prefix = Mage::getStoreConfig('epicor_hosting/file/prefix');
            foreach ($logFiles as $file) {
                if (!in_array($file, array('.', '..'))) {
                    if (strpos($file, $file_prefix) !== false) {
                        $files[] = new Varien_Object(array(
                            'filename' => $file,
                            'size' => filesize($logFileDir . $file),
                            'last_modified' => date('Y-m-d H:i:s', filemtime($logFileDir . $file)),
                        ));
                    }
                }
            }
        }

        return $files;
    }

    protected function _getColumns()
    {
        $columns = array();

        $columns['filename'] = array(
            'header' => Mage::helper('flexitheme')->__('Name'),
            'align' => 'left',
            'index' => 'filename',
            'type' => 'text'
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

        return $columns;
    }

    public function getRowUrl($row)
    {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $log = $helper->urlEncode($helper->encrypt($row->getFilename()));
        return $this->getUrl('*/*/view', array('log' => $log));
    }

}
