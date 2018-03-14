<?php
/**
 * AR Payments Payment
 *  Customer Period balances list Grid config
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Aged_Grid extends Epicor_Common_Block_Generic_List_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('customerconnect_arpayments_aged_grid');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('caps');
        $this->setIdColumn('invoice_number');
        $this->setCacheDisabled(false);
        $this->setHideRefreshMessage(true);
        $this->setShowAll(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);  
        //$this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);   
        $balanceInfo = $this->_getprepareCollection();
        if($balanceInfo) {
            $this->setCustomColumns($balanceInfo['columns']);
            $this->setCustomData($balanceInfo['balances']);
        } else {
            $this->setCustomColumns(array());
            $this->setCustomData(array());
        }
        $jsObjectName = $this->getJsObjectName();
        $this->setAdditionalJavaScript("
            $jsObjectName.resetFilter = function() {
                this.addVarToUrl(this.sortVar, '');
                this.addVarToUrl(this.dirVar,  '');                
                this.reload(this.addVarToUrl(this.filterVar, ''));
            }          
        ");         
    }
    
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $currency_symbol = Mage::app()->getLocale()->currency( $currency_code )->getSymbol();
        $checkCaapActive = Mage::getModel('customerconnect/arpayments')->checkCaapActive();
        /* @var $checkCaapActive Epicor_Customerconnect_Model_Arpayments */        
        $style="";
        if(!$checkCaapActive) {
            $style="display:none;";
        }        
        $html ='
                <div style="width:50%;'.$style.'">
                    <p>
                        <fieldset>
                            <label for="allocate_amount">Total Amount to Apply</label>
                            '.$currency_symbol.'<input name="allocate_amount" style="width:136px" maxlength="8"  id="allocate_amount" class="input-text no-changes allocate_amount" type="text">
                            <input  name="allocate_amount_original" id="allocate_amount_original"  type="hidden" value="">
                            <input name="payment_on_account" id="payment_on_account"   type="checkbox" ><label for="payment_on_account" style="padding-left:5px">Payment On Account</label>
                        </fieldset>
                        <fieldset style="clear:both;padding-top:10px" id="arpayment_left">
                            <label for="amount_left_ar" id="amount_left_ar_label">Amount Left</label>
                             <span style="padding-left:7px;">'.$currency_symbol.'</span><span class="amount_left_ar">0.00</span>
                             <input type="hidden" name="amount_left_ar" value="" id="amount_left_ar" class="amount_left_hiddenar"/>
                        </fieldset>                        
                    </p>
                    <div>
                        <p>
                            <br>
                            <button id="allocatebutton" title="Search" type="button" class="scalable task" onclick="calculateArSum()" style=""><span><span><span>Apply Payment</span></span>
                                </span>
                            </button>
                        </p>
                    </div>
                </div>
                <br>'. $html;
        return $html;
    }         
    
    
    protected function _getprepareCollection()
    {
        parent::_prepareCollection();
        $cols = $this->getCollection();
        $i=0;
        foreach ($cols as $balance) {
           $balanceData=  $balance->getData('aged_balances_aged_balance');
           $currencyCode = $balance->getData('currency_code');
        }
        $balances = array(
            0 => new Varien_Object()
        );
        $columnsVals = array();
        
        $countBalances = count($balanceData);
        if($countBalances =="1"){
          $balanceData = array(0 =>$balanceData);
        }
        
        if(!empty($balanceData)) {
            $cacheEnabled = Mage::app()->getCacheInstance()->canUse('customerconnect');
            foreach ($balanceData as $balance) {
                $number = $balance->getData('_attributes')->getNumber();
                $description = $balance->getDescription();
                $balanceCaps = $balance->getBalance();
                if(($number) && ($description) ) {
                    $setBalanceCaps = ($balanceCaps)? $balanceCaps : '0.00';
                    $balances[0]->setData($number, $setBalanceCaps);
                    $columnsVals['aged_balance_filter_'.$number] = array(
                        'header' => Mage::helper('epicor_comm')->__($description),
                        'align' => 'left',
                        'name' => 'aged_balance_filter',
                        'filter_index' => 'aged_balance_filter',
                        'type'  => 'currency',
                        'id' => 'aged_balance_filter_'.$number,
                        'currency_code' => Mage::helper('customerconnect')->getCurrencyMapping($currencyCode, Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO),
                        'index' => $number,
                        'renderer' => ($cacheEnabled) ? new Epicor_Customerconnect_Block_Customer_Arpayments_List_Renderer_Aged() : "",
                    );
                }
            }
        }
        ksort($columnsVals);
        return array(
            'balances' => $balances,
            'columns' => $columnsVals,
        );
    }   
    
    public function getRowUrl($row) {
        return false;
    }
    
    
    public function getRowClass(Varien_Object $row) {
        
        $outStandingAmount = $row->getOutstandingValue();
        $disableClass = false;
        if($outStandingAmount) {
            $disableClass =true;
        }        
        return $disableClass ? 'disable_aged_grid' : '';
    }  
    
}