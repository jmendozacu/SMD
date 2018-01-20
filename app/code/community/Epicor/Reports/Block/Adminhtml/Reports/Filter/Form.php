<?php

class Epicor_Reports_Block_Adminhtml_Reports_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Preparing form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::getModel('epicor_reports/rawdata');
        /* @var $model Epicor_Reports_Model_Rawdata */
        $dependenceBlock = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        /* @var $dependenceBlock Mage_Adminhtml_Block_Widget_Form_Element_Dependence */
        $helper = Mage::helper('epicor_reports');
        /* @var $helper Epicor_Reports_Helper_Data */
        $store = Mage::getSingleton('adminhtml/system_store');
        /* @var $store Mage_Adminhtml_Model_System_Store */
         
        $form = new Varien_Data_Form(
            array(
                'id' => 'filter_form',
                'action' => $this->getUrl('*/*/graph'),
                'method' => 'post',
            )
        );

        $htmlIdPrefix = 'messaging_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('Filter')));

        $dateFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('store_id', 'select', array(
            'name' => 'store_id',
            'label' => $this->__('Store(s)'),
            'title' => $this->__('Store(s)'),
            'required' => true,
            'values' => $this->_changeKeysStoresArray($store->getStoreValuesForForm(false, true)),
        ));

        $chartTypeInput = $fieldset->addField('chart_type', 'select', array(
            'name'      => 'chart_type',
            'options'   => $helper->getChartTypes(),
            'label'     => $this->__('Chart type'),
            'required'  => true,
            //TESTING
            'value'     => 'speed'
        ));

        $fieldset->addField('message_status', 'select', array(
            'name'      => 'message_status',
            'options'   => $helper->getMessageStatus(),
            'label'     => $this->__('Message status'),
            'value'     => 'combined'
        ));

        $fieldset->addField('message_type', 'multiselect', array(
            'name'      => 'message_type',
            'class'     => 'required-entry',
            'values'    => $helper->getMessageTypes(),
            'label'     => $this->__('Message type'),
            //TESTING
            'value'     => array('MSQ')
        ));

        $resolutionInput = $fieldset->addField('resolution', 'text', array(
            'name'      => 'resolution',
            'class'     => 'validate-number required-entry',
            'label'     => $this->__('Resolution'),
            'value'     => 1
        ));

        $resolutionUnitInput = $fieldset->addField('resolution_unit', 'select', array(
            'name'      => 'resolution_unit',
            'options'   => $helper->getMinMaxAvgResolutionUnits(),
            'label'     => $this->__('Unit of Resolution'),
            'value'     => $helper->getMinMaxAvgResolutionUnitDefault()
        ));

        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormat,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => $this->__('From'),
            'title'     => $this->__('From'),
            'required'  => true,
            //'value'     => '2014-10-05 01:16:00',
	    'value'     => date('Y-m-d', strtotime('-2 weeks')),
            'time'      => true
        ));

        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormat,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => $this->__('To'),
            'title'     => $this->__('To'),
            'required'  => true,
            'value'     => date('Y-m-d'),
            #'value'     => '2014-07-17 10:16:15',
            'time'      => true
        ));

        $fieldset->addField('cached', 'multiselect', array(
            'name'      => 'cached',
            'class'     => 'validate-chart-type',
            'values'    => $helper->getCachedValues(),
            'label'     => $this->__('Cached'),
            'value'     => 'none'
        ))->setSize(3);

//        $fieldset->addField('switched', 'radios', array(
//            'name'      => 'switched',
//            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
//            'label'     => $this->__('Switch chart'),
//            'value'     => 0
//        ));

        $dependenceBlock
                ->addFieldMap($chartTypeInput->getHtmlId(), $chartTypeInput->getName())
                ->addFieldMap($resolutionInput->getHtmlId(), $resolutionInput->getName())
                ->addFieldMap($resolutionUnitInput->getHtmlId(), $resolutionUnitInput->getName())
                ->addFieldDependence(
                    $resolutionInput->getName(),
                    $chartTypeInput->getName(),
                    $model::REPORT_TYPE_MIN_MAX_AVERAGE
                )
                ->addFieldDependence(
                    $resolutionUnitInput->getName(),
                    $chartTypeInput->getName(),
                    $model::REPORT_TYPE_MIN_MAX_AVERAGE
                );
        $this->setChild('form_after', $dependenceBlock);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _changeKeysStoresArray($array){
        foreach($array as &$item){
            if($item['value'] !== 0){
                if(is_array($item['value'])){
                    $item['value'] = $this->_changeKeysStoresArray($item['value']);
                }
                else{
                    $item['value'] = str_replace(html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8'), '', $item['label']);
                }
            }
        }
        return $array;
    }
}