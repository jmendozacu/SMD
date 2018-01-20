<?php

/**
 * Data controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_DataController extends Mage_Core_Controller_Front_Action
{

    /**
     * Offline Orders Test/Trigger Action
     */
    public function imagecleanupAction()
    {
        $cron = new Epicor_Common_Model_Cron();
        $cron->imageCleanup();
    }
    
    /**
     * Offline Orders Test/Trigger Action
     */
    public function crruAction()
    {
        $cron = new Epicor_Comm_Model_Cron();
        $cron->submitReturnsToErp();
    }

    /**
     * Offline Orders Test/Trigger Action
     */
    public function submitfilesAction()
    {
        $cron = new Epicor_Comm_Model_Cron();
        $cron->submitFilesToErp();
    }

    /**
     * Offline Orders Test/Trigger Action
     */
    public function offlineordersAction()
    {
        $cron = new Epicor_Comm_Model_Cron();
        $cron->offlineOrders();
    }

    /**
     * Scheduled MSQ Test/Trigger Action
     */
    public function schedulemsqAction()
    {
        $cron = new Epicor_Comm_Model_Cron();
        $cron->scheduleMsq();
    }

    /**
     * Scheduled SOD Test/Trigger Action
     */
    public function schedulesodAction()
    {
        echo '<pre>';
        $cron = new Epicor_Comm_Model_Cron();
        $cron->scheduleSod();
    }

    public function synlogcleanupAction()
    {
        echo '<pre>';
        $cron = new Epicor_Comm_Model_Cron();
        $cron->cleanupSynLog();
    }

    /**
     * Schedule/tester action for the Asyncronous Image assignment
     */
    public function scheduleimageAction()
    {
        echo '<pre>';
        echo 'Create Cron';
        $cron = new Epicor_Comm_Model_Cron_Product();
        echo "\n Schedule Image";
        $cron->scheduleImage();
    }

    public function postdebugAction()
    {
        echo '<pre>';
        $xml = trim(file_get_contents('php://input'));

        $helper = Mage::helper('epicor_common/xml');
        /* @var $helper Epicor_Common_Helper_Xml */
        print_r(getallheaders());
        #print_r(());
        print_r($_POST);
        print_r($_GET);
        echo $xml;
        exit;
    }

    /**
     * ERP to post data to this action
     */
    public function responderAction()
    {
        ini_set('memory_limit', '512M');
        $message_helper = Mage::helper('epicor_comm/messaging');
        /* @var $message_helper Epicor_Comm_Helper_Messaging */

        $xml = $message_helper->formatXml(trim(file_get_contents('php://input')));

        $response = $message_helper->processSingleMessage($xml, true);

        if ($response->getIsAuthorized()) {
            $httpStatusCode = 200;
        } else {
            $httpStatusCode = 403;
        }
//        ob_clean();
        $this->getResponse()
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHttpResponseCode($httpStatusCode)
                ->setHeader('Content-Length', strlen($response->getXmlResponse()));

        echo $response->getXmlResponse();
    }

    public function logcleanAction()
    {
        $log = Mage::getModel('epicor_comm/message_log');
        $log->clean();
    }

    public function queuecleanAction()
    {
        $queue = Mage::getModel('epicor_comm/message_queue');
        /* @var $queue Epicor_Comm_Model_Message_Queue */
        $queue->clean();
    }

    public function indexproductAction()
    {
        echo '<pre>';
        echo 'Create Cron';
        $cron = new Epicor_Comm_Model_Cron();
        echo "\n Schedule Image";
        $cron->productIndexer();
    }

    /**
     * Test Action
     */
    public function pkAction()
    {
        echo '<pre>';
        $helper = Mage::helper('epicor_comm/setup');
        /* @var $helper Epicor_Comm_Helper_Setup */
        $helper->addAccessElement('Epicor_Common', 'Account', 'changeForgotten', null, 'Access', 1, 1);
        echo "Done";
    }

    public function licforAction()
    {
        echo Mage::helper('epicor_comm')->licfor();
    }

    /**
     * Test Action
     */
    public function genecclicAction()
    {
        echo Mage::helper('epicor_comm')->genecclic();
    }

    public function updatesvnAction()
    {
        if (file_exists('svnupdate.sh')) {
            echo '<pre>';
            echo shell_exec('./svnupdate.sh');
            echo '</pre>';
            $this->clearcacheAction();
        }
    }

    public function enableagaAction()
    {
        if (file_exists('svnupdate.sh')) {
            echo '<pre>';
            echo shell_exec("sed -i 's/false/true/' ./app/etc/modules/Epicor_Aga.xml");
            echo shell_exec('rm -r ./var/cache/*');
            echo shell_exec('redis-cli flushall');
            echo '</pre>';
            $this->clearcacheAction();
        }
    }

    public function disableagaAction()
    {
        if (file_exists('svnupdate.sh')) {
            echo '<pre>';
            echo shell_exec("sed -i 's/true/false/' ./app/etc/modules/Epicor_Aga.xml");
            echo shell_exec('rm -r ./var/cache/*');
            echo shell_exec('redis-cli flushall');
            echo '</pre>';
            $this->clearcacheAction();
        }
    }

    public function clearcacheAction()
    {
        Mage::dispatchEvent('adminhtml_cache_flush_all');
        Mage::app()->cleanCache();
    }
/*
    public function postdataAction()
    {
        $xml = '';
        if ($this->getRequest()->getParam('xml')) {
            $xml = $this->getRequest()->getParam('xml');
            $_url = Mage::getStoreConfig('Epicor_Comm/xmlMessaging/url');
            $_api_username = Mage::getStoreConfig('Epicor_Comm/licensing/username');
            $_api_password = Mage::helper('epicor_comm')->decrypt(Mage::getStoreConfig('Epicor_Comm/licensing/password'));
            $_company = Mage::app()->getStore()->getWebsite()->getCompany() ? : Mage::app()->getStore()->getGroup()->getCompany();

            $connection = new Zend_Http_Client();
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $connection->setUri($_url);
//$adapter->setCurlOption(CURLOPT_URL, $this->url);
            $adapter->setCurlOption(CURLOPT_HEADER, FALSE);

            $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, FALSE);
            $adapter->setCurlOption(CURLOPT_SSL_VERIFYHOST, FALSE);
            $adapter->setCurlOption(CURLOPT_RETURNTRANSFER, 1);

// post options
            $adapter->setCurlOption(CURLOPT_POST, 1);
            $adapter->setCurlOption(CURLOPT_TIMEOUT, 60000);
            $connection->setAdapter($adapter);


            $connection->setHeaders('Content-Length', strlen($xml));
            if (Mage::getStoreConfig('Epicor_Comm/licensing/erp') == 'p21') {
                $connection->setHeaders('Authorization', 'Bearer ' . Mage::getStoreConfig('Epicor_Comm/licensing/p21_token'));
            } else {
                $connection->setAuth($_api_username, $_api_password);
            }

            $callSettings = array(
                'Company' => $_company
            );

//        if(is_array($this->_pools)) {
//            if(array_key_exists('eccweb_service', $this->_pools))
//                    $callSettings['ECCWebService'] = $this->_pools['eccweb_service'];
//            
//            if(array_key_exists('eccweb_form', $this->_pools))
//                    $callSettings['ECCWebForm'] = $this->_pools['eccweb_form'];
//        }

            $connection->setHeaders('CallSettings', base64_encode(json_encode($callSettings)));

            $connection->setRawData($xml, 'text/xml');
            $response = $connection->request(Zend_Http_Client::POST);
            $xml_back = $response->getBody();
            echo "<p><strong>URL : </strong>" . $_url . '</p>';
            echo "<p><strong>HTTP Status : </strong>" . $response->getStatus() . '</p>';


            $helper = Mage::helper('epicor_common/xml');
            

            $valid_xml = simplexml_load_string ($xml_back, 'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
            if (false == $valid_xml) {
                echo '<h2>Received non (or invalid) XML</h2>';
                echo nl2br(htmlentities($xml_back));
            } else {
                echo '<h2>Received XML</h2>';
                echo $helper->convertXmlToHtml($xml_back);
            }
        }

        echo '<hr>
                <form method="POST">
       <textarea name="xml" rows="30" cols="100" >' . $xml . '</textarea>        
<input type="submit" value="Send Xml" />
</form>
            ';
    }
*/
    /**
     * Generates a CSV that can be used for upload
     */
    public function generateCartCsvAction()
    {
        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=example_cart.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies

        $csvString = '"SKU","QTY","UOM"';
        if ($locHelper->isLocationsEnabled()) {
            $csvString .= ',"LOCATION"';
        }
        $csvString .= "\n" . '"ExampleProduct1","1","EA"';

        if ($locHelper->isLocationsEnabled()) {
            $csvString .= ',"Location 1"';
        }

        $this->getResponse()->setBody($csvString);
    }

}

if (!function_exists('getallheaders')) {

    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

}
