<?php

/**
 * Error report Grid
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Errors_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('advancedErrorsGrid');
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
        $reportFileDir = Mage::getBaseDir('var') . DS . 'report' . DS;

        $files = array();

        if (file_exists($reportFileDir)) {
            $reportFiles = scandir($reportFileDir);

            foreach ($reportFiles as $file) {
                if (!in_array($file, array('.', '..'))) {
                    $files[] = new Varien_Object(array(
                        'filename' => $file,
                        'last_modified' => date('Y-m-d H:i:s', filemtime($reportFileDir . $file)),
                    ));
                }
            }
        }

        return $files;
    }

    protected function _getColumns() {
        $columns = array();

        $columns['filename'] = array(
            'header' => Mage::helper('flexitheme')->__('Name'),
            'align' => 'left',
            'index' => 'filename',
            'condition' => 'LIKE'
        );

        $columns['last_modified'] = array(
            'header' => Mage::helper('flexitheme')->__('Last Modified'),
            'align' => 'left',
            'index' => 'last_modified',
            'type' => 'datetime',
        );

        return $columns;
    }

    public function getRowUrl($row) {
        $helper = Mage::helper('epicor_common');
        /* @var $helper Epicor_Common_Helper_Data */
        $report = $helper->urlEncode($helper->encrypt($row->getFilename()));
        return $this->getUrl('*/*/view', array('report' => $report));
    }

}
