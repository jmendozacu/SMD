<?php

/**
 * Data controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_TestController extends Mage_Core_Controller_Front_Action
{

    protected $_debug = false;

    public function gjAction()
    {
        echo '<pre>';
        $helper = Mage::helper('epicor_lists/frontend_product');
        /* @var $helper Epicor_Lists_Helper_Frontend_Product */

        var_dump($helper->getActiveListsProductIds());
        var_dump($helper->getContractsForProduct(6));
    }
    
    public function ggAction()
    {
        $helper = Mage::helper('epicor_lists/frontend_product');
        /* @var $helper Epicor_Lists_Helper_Frontend_Product */
//        
        $collection = Mage::getModel('epicor_lists/list')->getCollection();
        /* @var $collection Epicor_Lists_Model_Resource_List_Collection */

        $collection->filterActive();

        $filters = $helper->getListFilters();
        foreach ($filters as $filter) {
            $filter->filter($collection);
        }
        
        $collection->getSelect()->group('main_table.id');
        
        echo (string) $collection->getSelect();
        
        echo '<br>';
        var_dump(count($helper->getActiveLists()));
        foreach ($helper->getActiveLists() as $list) {
            echo '<br>' . $list->getTitle();
        }
    }

    public function pkAction()
    {
        echo '<pre>';
        $customer = Mage::getModel('customer/customer')->load(8);
        
        var_dump($customer->getData());
    }

    /**
     * Test Action
     */
    public function cccninAction()
    {
        echo '<pre>';
        $xml_str = '<?xml version="1.0" encoding="UTF-8"?>
<messages version="1.0">
  <request type="CCCN" id="95a483270f52e91701a4f7b3f5e449b7">
    <header>
      <datestamp>2013-08-12T13:54:25+00:00</datestamp>
      <source>ERP SIMulator</source>
      <erp>ECC</erp>
    </header>
    <body>
      <contract delete="N">
        <brands>
          <brand>
            <company>EPIC06</company>
            <site/>
            <warehouse/>
            <group/>
          </brand>
        </brands>
        <accountNumber>36</accountNumber>
        <contractCode>CTCO6</contractCode>
        <currencyCode>USD</currencyCode>
        <contractTitle>Contract1</contractTitle>
        <startDate>2016-01-01</startDate>
        <endDate>2016-12-12</endDate>
        <contractStatus>A</contractStatus>   
        <lastModifiedDate>2016-12-12</lastModifiedDate>
        <salesRep>Sales Re</salesRep>
        <contactName>Contact Name</contactName>
        <purchaseOrderNumber>PON11</purchaseOrderNumber>
        <contractDescription>sfcontractdescription2</contractDescription>
        <deliveryAddresses>
          <deliveryAddress>
            <addressCode>ADDCD2</addressCode>
            <purchaseOrderNumber>PON11</purchaseOrderNumber>
            <name>AddName</name>
            <address1>Add1</address1>
            <address2>Add2</address2>
            <address3>Add3</address3>
            <city>City</city>
            <county>County</county>
            <country>US</country>
            <postcode>0011</postcode>
            <telephoneNumber>TelNum</telephoneNumber>
            <faxNumber>FaxNum</faxNumber>
            <emailAddress>email@example.com</emailAddress>
          </deliveryAddress>
          <deliveryAddress>
            <addressCode>ADDCD2</addressCode>
            <purchaseOrderNumber>PON11</purchaseOrderNumber>
            <name>AddName</name>
            <address1>Add1</address1>
            <address2>Add2</address2>
            <address3>Add3</address3>
            <city>City2</city>
            <county>County</county>
            <country>US</country>
            <postcode>0011</postcode>
            <telephoneNumber>TelNum</telephoneNumber>
            <faxNumber>FaxNum</faxNumber>
            <emailAddress>email@example.com</emailAddress>
          </deliveryAddress>
        </deliveryAddresses>
        <parts>
          <part delete="N">
            <productCode>10320</productCode>
            <contractLineNumber>0001</contractLineNumber>
            <contractPartNumber>UOMP2</contractPartNumber>
            <effectiveStartDate>2016-02-02</effectiveStartDate>
            <effectiveEndDate>2016-11-11</effectiveEndDate>
            <lineStatus>A</lineStatus>
            <unitOfMeasures>
              <unitOfMeasure default="Y">
                <unitOfMeasureCode>EA</unitOfMeasureCode>
                <minimumOrderQty>0</minimumOrderQty>
                <maximumOrderQty>10</maximumOrderQty>
                <contractQty>10</contractQty>
                <isDiscountable>Y</isDiscountable>
                <currencies>
                  <currency>
                    <currencyCode>USD</currencyCode>
                    <contractPrice>10</contractPrice>
                    <breaks>
                      <break>
                        <quantity>2</quantity>
                        <contractPrice>5</contractPrice>
                        <discount>
                          <description>test br desc</description>
                        </discount>
                      </break>
                      <break>
                        <quantity>4</quantity>
                        <contractPrice>2</contractPrice>
                        <discount>
                          <description>test br desc</description>
                        </discount>
                      </break>
                    </breaks>
                    <valueBreaks>
                      <valueBreak>
                        <lineValue>x</lineValue>
                        <contractPrice>2</contractPrice>
                        <discount>
                          <description>testdesc</description>
                        </discount>
                      </valueBreak>
                    </valueBreaks>
                  </currency>
                </currencies>
              </unitOfMeasure>
            </unitOfMeasures>
          </part>
          <part delete="N">
            <productCode>JBRAND02</productCode>
            <contractLineNumber>0002</contractLineNumber>
            <contractPartNumber>UOMP3</contractPartNumber>
            <effectiveStartDate>2016-02-02</effectiveStartDate>
            <effectiveEndDate>2016-11-11</effectiveEndDate>
            <lineStatus>A</lineStatus>
            <unitOfMeasures>
              <unitOfMeasure default="N">
                <unitOfMeasureCode>EA</unitOfMeasureCode>
                <minimumOrderQty>2</minimumOrderQty>
                <maximumOrderQty>12</maximumOrderQty>
                <contractQty>14</contractQty>
                <isDiscountable>Y</isDiscountable>
                <currencies></currencies>
              </unitOfMeasure>
            </unitOfMeasures>
          </part>
        </parts>
      </contract>
    </body>
  </request>
</messages>';
        
   //     Mage::getModel('epicor_comm/message_upload')->debug($xml_str);
        $this->_schemeValidation($xml_str, 'upload' . DS . 'cccn.xsd');
    }
    /**
     * Test Action
     */
    public function cupginAction()
    {
        echo '<pre>';
        $xml_str = '<?xml version="1.0"?>
<messages version="1.0">
  <request type="CUPG" id="100008253">
    <header>
      <datestamp>2016-08-08T14:23:10+01:00</datestamp>
      <source>1</source>
      <erp>E10</erp>     
    </header>
    <body>
      <list delete="N">
        <brands>
          <brand>
            <company>EPIC06</company>
            <site>main</site>
            <warehouse/>
            <group/>
          </brand>
        </brands>
        <accounts>
          <accountNumber>36</accountNumber>
          <accountNumber>48</accountNumber>
        </accounts>
        <listCode>8sflistcode</listCode>
        <listTitle>8sfnewlisttitle</listTitle>
        <listSettings>8sflistsettings</listSettings>
        <listStatus>A</listStatus>
        <listDescription>sftestlistdesc8</listDescription>
        <products>
          <product delete="N">
            <productCode>JBRAND02</productCode>
            <unitOfMeasures>
              <unitOfMeasure>
                <unitOfMeasureCode>XX</unitOfMeasureCode
              </unitOfMeasure>
            </unitOfMeasures>
          </product>
        </products>
      </list>
    </body>
  </request>
</messages>';
        
   //     Mage::getModel('epicor_comm/message_upload')->debug($xml_str);
        $this->_schemeValidation($xml_str, 'upload' . DS . 'cupg.xsd');
    }

    public function stkoutAction()
    {
        $xml_str = '<?xml version="1.0"?>
<messages>
  <response type="STK" id="1234214214214412">
    <header>
      <datestamp>2013-12-23T11:32:41+00:00</datestamp>
      <source>Websales</source>
      <erp>Websales</erp>
    </header>
    <body>
      <status>
        <code>200</code>
        <description></description>
      </status>
    </body>
  </response>
</messages>';
        $this->debug($xml_str);





        $this->_schemeValidation($xml_str, 'upload' . DS . 'stk.xsd');
    }

    public function cusinAction()
    {
        $xml_str = '<?xml version="1.0" encoding="utf-8"?>
<messages xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <request type="CUS" id="100001553">
    <header>
      <datestamp>2013-12-24T12:10:18+00:00</datestamp>
      <source>1</source>
      <erp>E10</erp>
    </header>
    <body>
      <customer delete="N" allowBackOrders="N">
        <accountNumber>2</accountNumber>
        <accountName>Addison, INC</accountName>
        <taxCode>EU</taxCode>
        <brands>
          <brand>
            <company>EPIC03</company>
            <site />
            <warehouse />
            <group />
          </brand>
        </brands>
        <account onStop="N">
          <balance>56705.750</balance>
          <creditLimit>1000000</creditLimit>
          <unallocatedCash>0</unallocatedCash>
          <baseCurrencyCode>USD</baseCurrencyCode>
          <emailAddress />
          <salesRep>LANE</salesRep>
          <minOrderValue />
          <currencies>
            <currency>
              <currencyCode>USD</currencyCode>
            </currency>
            <currency>
              <currencyCode>EUR</currencyCode>
            </currency>
          </currencies>
        </account>
        <defaults>
          <registeredAddress>
            <addressCode>2RG</addressCode>
            <name>Addison, INC</name>
            <address1>210 Martin Luther King, Jr. Blvd</address1>
            <address2 />
            <address3 />
            <city>Madison</city>
            <county>WI</county>
            <country>USA</country>
            <postcode>53703</postcode>
            <telephoneNumber />
            <faxNumber />
          </registeredAddress>
          <deliveryAddress>
            <addressCode>001</addressCode>
            <name>Addison, INC</name>
            <address1>215 Martin Luther King, Jr. Blvd</address1>
            <address2>Door 2</address2>
            <address3 />
            <city>Madison</city>
            <county>WI</county>
            <country>USA</country>
            <postcode>53703</postcode>
            <telephoneNumber>608-555-5678</telephoneNumber>
            <faxNumber>608-555-5666</faxNumber>
            <carriageText>Leave in blue shed on Wednesdays please. </carriageText>
          </deliveryAddress>
          <invoiceAddress>
            <addressCode>2BT</addressCode>
            <name>Addison, INC</name>
            <address1>210 Martin Luther King, Jr. Blvd</address1>
            <address2 />
            <address3 />
            <city>Madison</city>
            <county>WI</county>
            <country>USA</country>
            <postcode>53703</postcode>
            <telephoneNumber />
            <faxNumber />
          </invoiceAddress>
          <registrationEmailAddress>test@epicor.com</registrationEmailAddress>
        </defaults>
      </customer>
    </body>
  </request>
</messages>';
        $this->debug($xml_str);
        $this->_schemeValidation($xml_str, 'upload' . DS . 'cus.xsd');
    }

    public function cusoutAction()
    {
        $xml_str = '<?xml version="1.0"?>
<messages>
  <response type="CUS" id="100001553">
    <header>
      <datestamp>2013-12-24T12:09:58+00:00</datestamp>
      <source>Websales</source>
      <erp>Websales</erp>
    </header>
    <body>
      <status>
        <code>200</code>
        <description></description>
      </status>
    </body>
  </response>
</messages>';
        $this->debug($xml_str);
        $this->_schemeValidation($xml_str, 'upload' . DS . 'cus.xsd');
    }
public function uploadXSDValidation($msgType)
    {
        $xml_str = '';
        $this->debug($xml_str);
        $this->_schemeValidation($xml_str, 'upload' . DS . "{$msgType}.xsd");
    }

    public function requestAction($msgType)
    {
        $xml_str = '';
        $this->debug($xml_str);
        $this->_schemeValidation($xml_str, 'request' . DS . "{$msgType}.xsd");
    }

    public function _schemeValidation($xml_str, $xsdFilename)
    {
        echo '<pre>';
        $filepath = (Mage::getModuleDir('base', 'Epicor_Common') . DS . 'xsd' . DS . $xsdFilename);
        libxml_use_internal_errors(true);
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->loadXML($xml_str);

        echo '<div id="valid">' . var_export($xml->schemaValidate($filepath), true) . '</div>';
        echo '<div id="errors">';
        var_dump(libxml_get_errors());
        libxml_use_internal_errors(false);
        echo '</div>';
    }

    public function debug($xml_str)
    {
        if ($this->_debug) {
            echo '<pre>';
            var_dump(htmlentities($xml_str));
            echo '<hr>';
        }
    }

    public function sfAction() {
       
        $x = Mage::helper('epicor_comm/returns')->isReturnsEnabled();
        return;
    }
    
    /**
     * Logs a message for the auto sync
     * 
     * @param string $message
     */
    protected function autoSyncLog($message)
    {
         Mage::log($message, null, 'auto_sync.log');
    }

    public function setConfigDates($startDateAndTime, $nextDateFromInSyn, $nextRunDateAndTime, $freqValue) {
        $nextRunDateAndTime = $nextRunDateAndTime ? $nextRunDateAndTime : $startDateAndTime;
        $currentTimestamp = time();
        $currentDateAndTime = strtotime(date(DATE_ATOM, $currentTimestamp));
        $twentyFourHourDateAndTime = strtotime(date("Y-m-d H:i:s", $currentTimestamp + 86400));
        $startTime = date("H:i:s", $startDateAndTime);
        $currentDate = strtotime(date("Y-m-d", $currentTimestamp));
        while ($currentDateAndTime > $nextRunDateAndTime) {
            $nextRunDateAndTime = $nextRunDateAndTime + $freqValue;
        };
        $nextRunDate = date("Y-m-d", $nextRunDateAndTime);
        if ($nextRunDateAndTime < $twentyFourHourDateAndTime) {
            $dateDiff = strtotime($nextRunDate) - $currentDate;
            $difference = $dateDiff / 86400;
            // if == 1, date is tomorrow
            if ($difference == 1) {
                $nextRunDateAndTime = strtotime($nextRunDate . " " . $startTime);
            }
        }
        
        $this->autoSyncLog('Setting Next run time to : ' . $nextRunDateAndTime);
        $this->autoSyncLog('Setting Next From Date to : ' . $nextDateFromInSyn);        
        Mage::getConfig()->saveConfig('epicor_comm_enabled_messages/syn_request/autosync_next_rundate', $nextRunDateAndTime, 'default', 0);
        Mage::getConfig()->saveConfig('epicor_comm_enabled_messages/syn_request/autosync_next_date_from_in_syn', $nextDateFromInSyn, 'default', 0);
        Mage::app()->cleanCache(array('CONFIG')); 
    }

    public function generateFileChainAction(){
        
        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Common_Helper_File */
        
        $file = Mage::getModel('epicor_common/file')->load($this->getRequest()->getParam('id'));
        
        $data = array(
            'web_file_id' => $file->getId(),
            'erp_file_id' => $file->getErpId()
        );
        
        $chain = base64_encode($helper->urlEncode(serialize($data)));
        
        echo $chain;
        
    }
    
    public function updateTranslationCsvFileAction() {      // languageId and code will only be populated from singleTranslate 
        $languageId = $this->getRequest()->getParam('id');
        $languageCode = $this->getRequest()->getParam('code');
        
        $updatesCollection = Mage::getModel('flexitheme/translation_updates')->getCollection();
        $updatesCollection->getSelect()->joinLeft(array('td' => 'epicor_flexitheme_translation_data'), 'main_table.phrase_id = td.id', array('td.translation_string'));
        $languageCode = ($languageCode)? $languageCode : $this->getRequest()->getParam('languageCode');
        $languageId = ($languageId)? $languageId : $this->getRequest()->getParam('id'); 
        $updatesCollection->addFieldToFilter('language_id', array('eq' => $languageId));
        $updates = $updatesCollection->getData();

        $path = Mage::getBaseDir('app');     // get base url 
        $localePath = $path .DS."design".DS."frontend".DS."base".DS."default".DS."locale".DS. $languageCode;

        if (!file_exists($localePath)) {    // check if locale directory exists and create it if not
            mkdir($localePath, 0777, true);
        }
        $file = $localePath .DS."translate.csv";
        $tempFile =  $localePath .DS. "translate" . date('YmdHis') . ".csv";
        if (file_exists($file)) {
            rename($file, $tempFile);
        }
        $fileContents = "";
        
        foreach ($updates as $update) {
            $fileContents .= "\"" . str_replace('"', '""',$update['translation_string']) . "\",\"" . str_replace('"', '""', $update['translated_phrase']) . "\"\n";
        }
        if(file_put_contents($file, $fileContents)){                    // write new translation file 
            if(file_exists($tempFile)){
                 unlink($tempFile);
            }        
        };  
        echo 'FILE UPDATED';
    }

}
