<?php

/**
 * List ERP Accounts Form
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Products_Import extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Import Products';
    }

    /**
     * Builds List ERP Accounts Form
     *
     * @return Epicor_Lists_Block_Adminhtml_List_Edit_Tab_Erpaccounts_Form
     */
    protected function _prepareForm()
    {
        $list = Mage::registry('list');
        /* @var $list Epicor_Lists_Model_List */
        
        $form = new Varien_Data_Form();
        
        if ($list->getTypeInstance()->isSectionEditable('products')) {
            $fieldset = $form->addFieldset('import_fields', array('legend' => $this->__('Product Import')));
            /* @var $fieldset Varien_Data_Form_Element_Fieldset */

            $fieldset->addField('productimportcsv', 'button', array(
                'value' => $this->__('Download Example CSV File'),
                'onclick' => "return listProduct.dowloadCsv();",
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

            $fieldset->addField('importSubmit', 'button', array(
                'value' => $this->__('Import'),
                'onclick' => "return listProduct.import();",
                'name' => 'importSubmit',
                'class' => 'form-button'
            ));
        }
        
        if ($list->getTypeInstance()->isSectionEditable('pricing')) {
            $form->addField('json_pricing', 'hidden', array(
                'name' => 'json_pricing',
            ));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
