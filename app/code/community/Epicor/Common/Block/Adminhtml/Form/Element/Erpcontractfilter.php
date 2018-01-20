<?php

class Epicor_Common_Block_Adminhtml_Form_Element_Erpcontractfilter extends Varien_Data_Form_Element_Abstract
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
            $contractFilter = $customer->getEccContractsFilter();  
            $noSelect  = (empty($contractFilter) ? "selected=selected" : "");
            $selectHtml = '<select name="' . $this->getName() . '" id="' . $attributeId . '"' . $this->serialize($this->getHtmlAttributes()) . ' class="select" multiple="multiple">';
            $selectHtml .= '<option value="" '.$noSelect.'>No Default Contract</option>';
            foreach ($contracts['items'] as $info) {
                $code      = $info['id'];
                $filterArray = explode(',', $contractFilter);
                $selected  = ($code == in_array($code, $filterArray) ? "selected=selected" : "");
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
                     var selectContracts = document.getElementById("'.$attributeId.'");
                     for (i = 0; i < selectContracts.options.length; i++) {
                     var currentOption = selectContracts.options[i];
                         if (currentOption.selected && currentOption.value =="") {
                            for (var i=1; i<selectContracts.options.length; i++) {
                                selectContracts.options[i].selected = false;
                            }                         
                         }
                     }
                });
            //]]>
            </script>'
            ;            

            $selectHtml .= $this->getAfterElementHtml();

        return $selectHtml;
    }


}
