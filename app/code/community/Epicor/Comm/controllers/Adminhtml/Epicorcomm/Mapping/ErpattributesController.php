<?php

/*
 * Controller for epicor_comm_erp_attributes grid  
 * in admin/epicorcomm_mapping_erpattributes
 */

class Epicor_Comm_Adminhtml_Epicorcomm_Mapping_ErpattributesController extends Epicor_Comm_Controller_Adminhtml_Abstract {
    /*
     * called by admin/epicorcomm_mapping_erpattributes/index 
     * blocks defined in adminhtml epicor_comm.xml 
     */

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/new 
     * 
     */

    public function newAction() {
        $this->_forward('edit');
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/edit 
     * Blocks defined in adminhtml_epicorcomm_mapping_erpattributes_edit 
     */

    public function editAction() {

        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('epicor_comm/erp_mapping_attributes');
        if ($id) {
            $model->load($id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('Attribute Type does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('erpattributes_mapping_data', $model);

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/edit 
     * Blocks defined in adminhtml_epicorcomm_mapping_erpattributes_edit 
     */

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('epicor_comm/erp_mapping_attributes');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('epicor_comm')->__('Error Saving Attribute Yype'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__('Attribute Type Was Successfully Saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('epicor_comm')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/delete
     */

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('epicor_comm/erp_mapping_attributes');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('epicor_comm')->__("Attribute has been deleted."));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the attribute to delete.'));
        $this->_redirect('*/*/');
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/exportCsv    
     */

    public function exportCsvAction() {
        $fileName = 'erpattributes.csv';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_erpattributes_grid')
                ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/exportXml    
     */

    public function exportXmlAction() {
        $fileName = 'erpattributes.xml';
        $content = $this->getLayout()->createBlock('epicor_comm/adminhtml_mapping_erpattributes_grid')
                ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /*
     * called by admin/epicorcomm_mapping_erpattributes/addByCsv 
     * Blocks defined in adminhtml_epicorcomm_mapping_erpattributes_addbycsv
     */

    public function addByCsvAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Generates a CSV that can be used for create a new list
     *
     * @return void
     */
    public function createNewErpattributesCsvAction() {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename= example_create_new_attribute.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies   
        $attributeTypes = implode("; ", array_keys(Mage::helper('epicor_comm')->_getEccattributeTypes(false)));
        
        $tableColumns = array_flip(array_keys(Mage::getResourceModel('epicor_comm/erp_mapping_attributes')->getFields()));
        unset($tableColumns['id']);
        $tableColumns = implode(",", array_flip($tableColumns));
        $header = '### Attribute Code             : Alphanumeric' . "\n" .
                '### Input Type                 : ' . $attributeTypes . "\n" .
                '### Separator                  : When uploading multiple options. Only used if input type is multiselect' . "\n" .
                '### Use For Config             : When uploading configurable products. Only used if input type is select (ie dropdown)' . "\n" .
                '### Quick Search               : Y/N' . "\n" .
                '### Advanced Search            : Y/N' . "\n" .
                '### Search Weighting           : Numeric' . "\n" .
                '### Use In Layered Navigation  :  0:null; 1:filterable(with results; 2:filterable(no results)' . "\n" .
                '### Search Results             : Y/N' . "\n" .
                '### Visible On Product View    : Y/N' . "\n" .
                '###' . "\n";
        ;
        $csvString = $header . $tableColumns;

        $this->getResponse()->setBody($csvString);
    }

    /**
     * Uploads CSV file
     *
     * @return void
     */
    public function csvuploadAction() {
        if ($this->getRequest()->isPost()) {
            $helper = Mage::helper('epicor_comm');
            /* @var $helper Epicor_Comm_Helper_Data */
            $result = $helper->importAttributeMappingFromCsv($_FILES['csv_file']['tmp_name']);
            $this->_redirect('*/*/addbycsv');
        } else {
            $this->_redirect('*/*/addbycsv');
        }
    }

}
