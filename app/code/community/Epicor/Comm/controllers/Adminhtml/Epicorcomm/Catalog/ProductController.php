<?php

/**
 * Product controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
include_once('Mage' . DS . 'Adminhtml' . DS . 'controllers' . DS . 'Catalog' . DS . 'ProductController.php');

class Epicor_Comm_Adminhtml_Epicorcomm_Catalog_ProductController extends Epicor_Comm_Controller_Adminhtml_Abstract
{

    public function locationsgridAction()
    {
        $this->loadLayout(false)->renderLayout();
    }

    public function deletelocationAction()
    {
        $locationId = $this->getRequest()->getParam('id');
        $locationProduct = Mage::getModel('epicor_comm/location_product')->load($locationId);
        $locationProduct->delete();
        echo $locationProduct->getLocationCode();
    }

    public function locationpostAction()
    {
        $data = new Varien_Object($this->getRequest()->getParams());
        if ($data->getManufacturers()) {
            $manData = array();
            $first = true;
            foreach ($data->getManufacturers() as $manRowData) {
                if (!empty($manRowData['name']) && !empty($manRowData['product_code'])) {
                    if ($first) {
                        $manRowData['primary'] = 'Y';
                    } else {
                        $manRowData['primary'] = 'N';
                    }
                    $manData[] = $manRowData;
                    $first = false;
                }
            }
            $data->setManufacturers(serialize($manData));
        }
        if ($data->getId() == '') {
            $data->unsId();
        }
        $productLocation = Mage::getModel('epicor_comm/location_product')->load($data->getId());
        /* @var $productLocation Epicor_Comm_Model_Location_Product */
        $productLocation->setData($data->getData());

        $currencyData = $productLocation->getCurrency($data->getCurrencyCode());
        /* @var $currencyData Epicor_Comm_Model_Location_Product_Currency */
        if($currencyData === false) {
            $currencyData = Mage::getModel('epicor_comm/location_product_currency');
            $currencyData->setProductId($productLocation->getProductId());
            $currencyData->setLocationCode($productLocation->getLocationCode());
            $currencyData->setCurrencyCode($data->getCurrencyCode());
        }
        $currencyData->setBasePrice($data->getBasePrice());
        $currencyData->setCostPrice($data->getCostPrice());
        
        $productLocation->setCurrency($currencyData);
        $productLocation->save();
    }

    public function logsgridAction()
    {
        $this->_initProduct();
        $this->loadLayout(false)->renderLayout();
    }

    public function skugridAction()
    {
        $this->_initProduct();
        $this->loadLayout(false)->renderLayout();
    }

}
