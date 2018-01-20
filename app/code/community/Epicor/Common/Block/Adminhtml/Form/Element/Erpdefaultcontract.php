<?php

class Epicor_Common_Block_Adminhtml_Form_Element_Erpdefaultcontract extends Varien_Data_Form_Element_Abstract
{




    /**
     * @return string
     */
    public function getElementHtml()
    {
            /* @var $customer Epicor_Comm_Model_Customer */
            $customer = Mage::registry('current_customer');
            $attributeId = $this->getHtmlId();
            $contracts = Mage::helper('epicor_common')->customerListsById($customer->getId(),'filterContracts');
            $defaultContract = $customer->getEccDefaultContract();  
            $selectHtml = '<select name="' . $this->getName() . '" id="' . $attributeId . '"' . $this->serialize($this->getHtmlAttributes()) . ' class="select">';
            $selectHtml .= '<option value="">No Default Contract</option>';
            foreach ($contracts['items'] as $info) {
                $code      = $info['id'];
                $selected  = ($code == $defaultContract ? "selected=selected" : ""); 
                $selectHtml .= '<option value="' . $code . '" ' . $selected . '>' . $info['title'] . '</option>';
            }
            $selectHtml .= '</select>';
            $selectHtml .= '<input type="hidden" name="ajax_url" id="ajax_url" value="'.Mage::helper("adminhtml")->getUrl("adminhtml/epicorcommon_customer/fetchaddress/",array()).'" />';
            $selectHtml .= '<input type="hidden" name="user_id" id="user_id" value="'.$customer->getId().'" />';
            $selectHtml .='<div id="loading-mask" style="display:none">
                            <p class="loader" id="loading_mask_loader">Please wait...</p>
                            </div>';
            $selectHtml .= '
            <script type="text/javascript">
            //<![CDATA[
                Event.observe("' . $attributeId . '", "change", function(event) {
                    el = Event.element(event);
                    var getcontractDefault = ' . $attributeId . '.options[' . $attributeId . '.selectedIndex].value;
                    $("loading-mask").show();
                    contractAddressChange(getcontractDefault); 
                });
                document.observe("dom:loaded", function(){
                    var contract_default = document.getElementById("'.$attributeId.'");
                    var getcontractDefault = ' . $attributeId . '.options[' . $attributeId . '.selectedIndex].value;
                    contractAddressChange(getcontractDefault);
                });
            //]]>
            </script>'
            ;            

            $selectHtml .= $this->getAfterElementHtml();

        return $selectHtml;
    }


}
