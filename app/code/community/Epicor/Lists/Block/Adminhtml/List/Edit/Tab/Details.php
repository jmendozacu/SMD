<?php

/**
 * List Details Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Details';
    }

    /**
     * Builds List Details Form
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Details
     */
    protected function _prepareForm()
    {
        $list = $this->getList();
        /* @var $list Epicor_Lists_Model_List */

        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (empty($formData)) {
            $formData = $list->getData();
        } else {
            $list->addData($formData);
        }
        $contractStatus = null;
        if ($list->getType() == "Co") {
            $contract = Mage::getModel('epicor_lists/contract')->load($list->getId(), 'list_id');
            $contractStatus = ($contract->getContractStatus() == "A") ? "Active" : "Inactive";
            $formData['contact_name'] = $contract->getContactName();
            $formData['po_number'] = $contract->getPurchaseOrderNumber();
            $formData['sales_rep'] = $contract->getSalesRep();
            $formData['last_modified_date'] = $contract->getLastModifiedDate();
            $formData['last_used_time'] = $contract->getLastUsedTime();
            $formData['contract_status'] = $contractStatus;
        }

        $this->addPrimaryFields($form, $list);
        $this->addActiveFields($form, $list, $contractStatus);
        $this->addFlagFields($form, $list);
        $this->addTypeSpecificFields($form, $list);

        if ($list->isObjectNew()) {
            $this->addImportFields($form, $list);
        } else {
            $this->addErpFields($form, $list);
        }

        $formData['settings'] = $list->getSettings();
        $formData['erp_override'] = $list->getErpOverride();


       


        $format = Varien_Date::DATETIME_INTERNAL_FORMAT;

        if ($list->getStartDate()) {
            $date = Mage::app()->getLocale()
                            ->date(
                                    $list->getStartDate(), $format
                            )->toString($format);
            $dateSplit = explode(' ', $date);
            $formData['start_date'] = $dateSplit[0];
            $formData['start_time'] = str_replace(':', ',', $dateSplit[1]);
        }

        if ($list->getEndDate()) {
            $date = Mage::app()->getLocale()
                            ->date(
                                    $list->getEndDate(), $format
                            )->toString($format);
            $dateSplit = explode(' ', $date);
            $formData['end_date'] = $dateSplit[0];
            $formData['end_time'] = str_replace(':', ',', $dateSplit[1]);
        }

        // need this line for contracts to hide uom separator
        $formData['erp_code'] = $list->getErpCode();
        $form->addValues($formData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Adds Primary fields to the form
     *
     * @param Varien_Data_Form $form
     * @param Epicor_Lists_Model_List $list
     *
     * @return void
     */
    protected function addPrimaryFields($form, $list)
    {
        $fieldset = $form->addFieldset('primary', array('legend' => $this->__('Primary Details')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $disableEdit = ($list->getType() == "Co") ? true : false;
        if ($list->getType() == "Co") {
            $fieldset->addField(
                    'title', 'text', array(
                'label' => $this->__('Title'),
                'required' => true,
                'name' => 'title',
                'readonly' => 'readonly'
                    )
            );
        } else {
            $fieldset->addField(
                    'title', 'text', array(
                'label' => $this->__('Title'),
                'required' => true,
                'name' => 'title'
                    )
            );
        }
        
        $args = array();
        if ($list->isObjectNew()) {
            $fieldset->addField(
                'listcodeurl', 'hidden', array(
                'name' => 'listcodeurl',
                'value' => $this->getUrl('adminhtml/epicorlists_list/validateCode', $args)
                )
            );
        }

        $disableFields = $list->isObjectNew() == false;

        $fieldset->addField(
                'type', 'select', array(
            'label' => $this->__('Type'),
            'name' => 'type',
            'values' => Mage::getModel('epicor_lists/list_type')->toOptionArray($list->isObjectNew()),
            'disabled' => $disableFields
                )
        );

        $fieldset->addField(
                'erp_code', 'text', array(
            'label' => $this->__('Code'),
            'required' => true,
            'name' => 'erp_code',
            'class' => $list->isObjectNew() ? 'required-entry validate-list-code' : '',
            'disabled' => $disableFields,
            'note' => $this->__('Unique reference code for this list')
                )
        );
        
        $fieldset->addField(
                'code_allowed', 'hidden', array(
            'name' => 'code_allowed',
            'label' => 'code_allowed',
        ));

//        $fieldset->addField(
//            'label', 'text', array(
//            'label' => $this->__('Default Label'),
//            'required' => false,
//            'name' => 'label'
//            )
//        );

        if ($list->isObjectNew() == false) {
            $fieldset->addField(
                    'source', 'text', array(
                'label' => $this->__('Source'),
                'required' => false,
                'name' => 'source',
                'disabled' => true,
                    )
            );
        }

        $fieldset->addField(
                'notes', 'textarea', array(
            'label' => $this->__('Notes'),
            'required' => false,
            'name' => 'notes'
                )
        );
        $fieldset->addField(
                'description', 'textarea', array(
            'label' => $this->__('Description'),
            'required' => false,
            'name' => 'description'
                )
        );
    }

    /**
     * Adds the Type specific details
     *
     * @return Epicor_Lists_Model_List
     */
    protected function addTypeSpecificFields($form, $list)
    {
        $fieldset = $form->addFieldset('typespecific', array('legend' => $this->__('Type Specific Details')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $disableEdit = ($this->getList()->getType() == "Co") ? true : false;

        $fieldset->addField(
                'sales_rep', 'text', array(
            'label' => $this->__('Sales Rep'),
            'name' => 'sales_rep',
            'disabled' => 1
                )
        );

        $fieldset->addField(
                'contact_name', 'text', array(
            'label' => $this->__('Contact Name'),
            'name' => 'contact_name',
            'disabled' => 1
                )
        );

        $fieldset->addField(
                'po_number', 'text', array(
            'label' => $this->__('PO Number'),
            'name' => 'po_number',
            'disabled' => $disableEdit
                )
        );

        if ($this->getList()->getType() == "Co") {
            $fieldset->addField(
                    'last_modified_date', 'text', array(
                'label' => $this->__('Last Modified Date'),
                'name' => 'last_modified_date',
                'disabled' => 1
                    )
            );
            
            $fieldset->addField(
                    'last_used_time', 'text', array(
                'label' => $this->__('Last Used Date & Time'),
                'name' => 'last_used_time',
                'disabled' => 1
                    )
            );
        }
    }

    /**
     * Adds Primary fields to the form
     *
     * @param Varien_Data_Form $form
     *
     * @return void
     */
    protected function addActiveFields($form, $list, $contractStatus = null)
    {
        $fieldset = $form->addFieldset('active_fields', array('legend' => $this->__('Active Details')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $disableEdit = ($this->getList()->getType() == "Co") ? true : false;

        $hideActiveUi = false;

        if ($list->getType() == "Co") {
            $fieldset->addField(
                    'contract_status', 'text', array(
                'label' => $this->__('ERP contract status'),
                'name' => 'contract_status',
                'disabled' => 1
                    )
            );
            $hideActiveUi = ($contractStatus == "Active") ? 0 : 1;
        }

        if (empty($hideActiveUi)) {
            $fieldset->addField(
                    'active', 'checkbox', array(
                'label' => $this->__('Is Active?'),
                'tabindex' => 1,
                'value' => 1,
                'name' => 'active',
                'checked' => $this->getList()->getActive()
                    )
            );

            $after = '<small>YYYY-MM-DD</small><br /><small>To Update Date Click on Calendar Icon</small>';

            $fieldset->addField(
                    'start_date', 'date', array(
                'label' => $this->__('From Date'),
                'comment' => 'Change Date Using Date Picker',
                'disabled' => $disableEdit,
                'tabindex' => 1,
                'class' => 'validate-date',
                'required' => false,
                'name' => 'start_date',
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => 'yyyy-MM-dd',
                'after_element_html' => $after,
                    )
            );
            $fieldset->addField(
                    'select_start_time', 'checkbox', array(
                'label' => $this->__('Select Time for "From Date"?'),
                'tabindex' => 1,
                'name' => 'select_start_time',
                'disabled' => $disableEdit,
                    )
            );
            $fieldset->addField(
                    'start_time', 'time', array(
                'label' => $this->__('Start Time'),
                'tabindex' => 1,
                'class' => 'validate-time',
                'required' => false,
                'name' => 'start_time',
                'readonly' => true,
                'format' => 'hh:mm:ss',
                'comment' => 'hh:mm:ss',
                'disabled' => $disableEdit,
                    )
            );

            $fieldset->addField(
                    'end_date', 'date', array(
                'label' => $this->__('To Date'),
                'comment' => 'Change Date Using Date Picker',
                'tabindex' => 1,
                'class' => 'validate-date',
                'required' => false,
                'name' => 'end_date',
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => 'yyyy-MM-dd',
                'after_element_html' => $after,
                'disabled' => $disableEdit,
                    )
            );

            $fieldset->addField(
                    'select_end_time', 'checkbox', array(
                'label' => $this->__('Select Time for "To Date"?'),
                'tabindex' => 1,
                'name' => 'select_end_time',
                'disabled' => $disableEdit,
                    )
            );
            $fieldset->addField(
                    'end_time', 'time', array(
                'label' => $this->__('End Time'),
                'tabindex' => 1,
                'class' => 'validate-time',
                'name' => 'end_time',
                'readonly' => true,
                'format' => 'hh:mm:ss',
                'comment' => 'hh:mm:ss',
                'disabled' => $disableEdit,
                    )
            );

            $isEnabledJs = $fieldset->addField('is_enabled_js', 'hidden', false);
            $isEnabledJs->setAfterElementHtml('
                    <script>
                        var start_time = $("start_time").up("td").select("select");
                        var end_time = $("end_time").up("td").select("select");

                        //set default end time to 23:59:59, if required
                        if(end_time[0].value == "00" && end_time[1].value == "00" && end_time[2].value == "00"){
                            end_time[0].value = "23";
                            end_time[1].value = "59";
                            end_time[2].value = "59";
                        }    
                        if(start_time[0].value > "00" || start_time[1].value > "00" || start_time[2].value > "00"){
                            $("select_start_time").checked = true;
                        }
                        if(end_time[0].value != "23" || end_time[1].value != "59" || end_time[2].value != "59"){
                            $("select_end_time").checked = true;                    
                        }

                        // the map functions loops through the array to avoid the need to repeat the code  
                        ["start","end"].map(function(item){
                            if($("select_" + item + "_time").checked){
                                $("select_" + item + "_time").value = 1;
                                $$("label[for ^=\'"+ item + "_time\']").each(function(a){  
                                    a.up("tr").select("td").each(function(b){
                                        b.show();
                                    })
                                })                       
                            }
                            else{
                                $("select_" + item + "_time").value = 0;
                                $$("label[for ^=\'"+ item + "_time\']").each(function(a){  
                                    a.up("tr").select("td").each(function(b){
                                            b.hide();
                                    })  
                                })    
                            }
                            $("select_" + item + "_time").observe("change", function(){ 
                                if(this.checked){
                                  $("select_" + item + "_time").value = 1;  
                                  $$("label[for ^=\'"+ item + "_time\']").each(function(a){  
                                    a.up("tr").select("td").each(function(b){
                                            b.show();
                                    }) 
                                    // if start ticked show end also
                                   if(item == "start"){
                                        $("select_end_time").value = 1;  
                                        $("select_end_time").checked = 1;  
                                        $$("label[for ^=\'end_time\']").each(function(a){  
                                          a.up("tr").select("td").each(function(b){
                                                b.show();
                                          })
                                        })  
                                   }
                                }) 
                                }else{
                                  $("select_" + item + "_time").value = 0;  
                                  $$("label[for ^=\'"+ item + "_time\']").each(function(a){  
                                    a.up("tr").select("td").each(function(b){
                                            b.hide();
                                    })
                                    // if start unticked hide end also
                                    if(item == "start"){
                                        $("select_end_time").value = 0;  
                                        $("select_end_time").checked = 0;  
                                        $$("label[for ^=\'end_time\']").each(function(a){  
                                          a.up("tr").select("td").each(function(b){
                                                b.hide();
                                          })
                                        })  
                                   }
                                  }) 
                                }  
                           });
                        })   

                    </script>
                    ');
        }
    }

    /**
     * Adds Primary fields to the form
     *
     * @param Varien_Data_Form $form
     * @param Epicor_Lists_Model_List $list
     *
     * @return void
     */
    protected function addFlagFields($form, $list)
    {

        $typeInstance = $list->getTypeInstance();

        $settings = Mage::getModel('epicor_lists/list_setting');
        /* @var $settings Epicor_Lists_Model_List_Setting */

        $fieldset = $form->addFieldset('settings_fields', array('legend' => $this->__('Settings')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $disableEdit = ($this->getList()->getType() == "Co") ? true : false;

        $supported = $typeInstance->getSupportedSettings();

        if (count($supported) > 0) {
            $fieldset->addField(
                    'settings', 'checkboxes', array(
                'label' => $this->__('Settings'),
                'name' => 'settings[]',
                'options' => $settings->toOptionArray($supported),
                'checked' => $list->getSettings(),
                'disabled' => ($disableEdit) ? $supported : 0,
                    )
            );
        }

        $fieldset->addField(
                'priority', 'text', array(
            'label' => $this->__('Priority'),
            'name' => 'priority',
            'class' => 'validate-number',
            'disabled' => $disableEdit
                )
        );
        
        if (($list->getSource() == "customer") && ($list->getOwnerId() != "")) {
            $Customer = Mage::getModel("customer/customer");
            $getCreatedBy = $Customer->load($list->getOwnerId());
            $emailId  = $getCreatedBy->getEmail();
            $fieldset->addField('ownerids', 'label', array(
                'label' => $this->__('Created By'),
                'name' => 'ownerids',
                'value' => $emailId,
                'disabled' =>1
            ));
        }        

        if ($list->isObjectNew()) {
            $typeModel = Mage::getModel('epicor_lists/list_type');
            /* @var $typeModel Epicor_Lists_Model_List_Type */

            $instance = Mage::getModel('epicor_lists/list_type_abstract');
            /* @var $instance Epicor_Lists_Model_List_Type_Abstract */

            $fieldset->addField(
                    'supported_settings_all', 'hidden', array(
                'name' => 'supported_settings_all',
                'value' => implode('', $instance->getSupportedSettings())
                    )
            );

            foreach ($typeModel->getTypeInstances() as $type => $instanceName) {
                $instance = Mage::getModel('epicor_lists/list_type_' . $instanceName);
                /* @var $instance Epicor_Lists_Model_List_Type_Abstract */
                $fieldset->addField(
                        'supported_settings_' . $type, 'hidden', array(
                    'name' => 'supported_settings_' . $type,
                    'value' => implode('', $instance->getSupportedSettings())
                        )
                );
            }
        }
    }

    /**
     * Adds Primary fields to the form
     *
     * @param Varien_Data_Form $form
     *
     * @return void
     */
    protected function addImportFields($form, $list)
    {
        $fieldset = $form->addFieldset('import_fields', array('legend' => $this->__('Product Import')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $fieldset->addField('productimportcsv', 'button', array(
            'value' => $this->__('Download Example CSV File'),
            'onclick' => "return window.location = '" . $this->getUrl('adminhtml/epicorlists_list/productimportcsv') . "';",
            'name' => 'productimportcsv',
            'class' => 'form-button'
        ));

        $fieldset->addField(
                'import', 'file', array(
            'label' => $this->__('CSV File'),
            'name' => 'import',
            'note' => $this->__('CSV containing 2 columns: "SKU" (required), "UOM" (optional). If no UOM provided, all UOMs for SKU will be added')
                )
        );
    }

    /**
     * Adds Primary fields to the form
     *
     * @param Varien_Data_Form $form
     * @param Epicor_Lists_Model_List $list
     *
     * @return void
     */
    protected function addErpFields($form, $list)
    {
        $typeInstance = $list->getTypeInstance();

        if ($typeInstance && $typeInstance->hasErpMsg()) {
            $msgName = $typeInstance->getErpMsg();
            $legend = array('legend' => $this->__('Overwritten On %s Update', $msgName));
            $fieldset = $form->addFieldset('erp_override_fields', $legend);
            /* @var $fieldset Varien_Data_Form_Element_Fieldset */
            
            $erpOverride = $list->getErpOverride();
            
            $msgSections = $typeInstance->getErpMsgSections();
            foreach ($msgSections as $value => $title) {
                $fieldset->addField('erp_override_' . $value, 'select', array(
                    'label' => $title,
                    'name' => 'erp_override[' . $value . ']',
                    'values' => Mage::getModel('epicor_comm/config_source_yesnonulloption')->toOptionArray(),
                    'value' => isset($erpOverride[$value]) ? $erpOverride[$value] : null
                ));
            }
        }
    }

    /**
     * Gets the current List
     *
     * @return Epicor_Lists_Model_List
     */
    public function getList()
    {
        if (!$this->_list) {
            $this->_list = Mage::registry('list');
        }
        return $this->_list;
    }

}
