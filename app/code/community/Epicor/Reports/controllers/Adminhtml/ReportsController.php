<?php
class Epicor_Reports_Adminhtml_ReportsController extends Epicor_Comm_Controller_Adminhtml_Abstract
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('epicor_common');
        $this->_title($this->__("Messaging Reports"));
        $this->_addBreadcrumb(Mage::helper('epicor_reports')->__('Form'), Mage::helper('epicor_reports')->__('Form'));

        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initAction();
        $this->_initLayoutMessages('reports/session');
        $this->renderLayout();
    }

    public function graphAction()
    {
        $post = $this->getRequest()->getPost();
        /*$post = array(
            'store_id' => 0,
            'chart_type' => 'performance',
            'message_status' => 'both',
            'message_type' => array('GOR'),
            'from' => '2014-07-14',
            'to' => '2014-07-16'
        );*/
        $this->loadLayout();
        $this->_initLayoutMessages('reports/session');
        $this->getLayout()->getBlock('root')->setChartOptions($post);
        $this->renderLayout();
    }

    public function reprocessAction()
    {
        /* @var $model Epicor_Reports_Model_Rawdata */
        $model = Mage::getModel('epicor_reports/rawdata');
        $model->reprocessMessageLogData();
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        /**
         * @var $helper Epicor_Reports_Helper_Data
         * @var $testBlock Epicor_Comm_Block_Adminhtml_Message_Log_Grid
         */
        $chartOptions = $this->getRequest()->getPost();
        $helper = Mage::helper('epicor_reports');
        $results = $helper->chartResults($chartOptions);
        if(sizeof($results) > 0){
            $fileName   = 'messaging_report.csv';
            $headers = sizeof($results) > 0 ? array_keys($results[0]) : array();
            $content = $this->getCsvFile($headers, $results);
            $this->_prepareDownloadResponse($fileName, $content);
        }
        else{
            Mage::getSingleton('reports/session')->addWarning(Mage::helper('epicor_reports')->__('No data to export'));
            $this->_redirect('*/*/index');
        }
    }

    function getCsvFile($headers, $rows){

        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($headers);

        foreach($rows as $row){
            $io->streamWriteCsv(array_values($row));
        }

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }
}