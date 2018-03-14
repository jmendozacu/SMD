<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Autocomplete queries list
 */
class Epicor_Comm_Block_Cart_Quickadd_Autocomplete extends Mage_Core_Block_Abstract
{

    protected $_suggestData = null;
    protected $_lastRowId = null;
    protected $_lastGroupChildData = null;
    protected $_enteredSku = null;
    protected $_pageData = null;
    protected $_page = 1;
    protected $_rowCount = 0;
    protected $_lastChildId = 0;

    protected function _toHtml()
    {       
        $html = '';

        if (!$this->_beforeToHtml()) {
            return $html;
        }

        $helper = Mage::helper('epicor_comm');
        /* @var $helper Epicor_Comm_Helper_Data */
        $contractHelper = Mage::helper('epicor_lists/frontend_contract');
        /* @var $contractHelper Epicor_Lists_Helper_Frontend_Contract */
        $contractProductHelper = Mage::helper('epicor_lists/frontend_product');
        /* @var $contractProductHelper Epicor_Lists_Helper_Frontend_Product */
        $page_params = $this->getRequest()->getParam('sku');
        $this->_enteredSku = $this->getRequest()->getParam('sku');
        $this->_pageData = $this->getRequest()->getParam('qa_page_data');
        $this->_page = $this->getRequest()->getParam('qa_page', 1);

        if ($this->_pageData == null) {
            $this->_pageData[1] = array('row_count' => $this->_rowCount, 'last_child_id' => $this->_lastChildId);
        } else {
            $this->_pageData = unserialize($helper->eccDecode($this->_pageData));
        }

        $this->_rowCount = $this->_pageData[$this->_page]['row_count'];
        $this->_lastChildId = $this->_pageData[$this->_page]['last_child_id'];
        
        $products = $this->getSuggestData();

        $perPage = Mage::getStoreConfig('quickadd/autocomplete_result_limit');

        $x = (($this->_page - 1) * $perPage) + 1;
        $maxX = $x + $perPage - 1;
        $rowId = '';
        $groupSku = '';
        $groupChildData = '';

        $locHelper = Mage::helper('epicor_comm/locations');
        /* @var $locHelper Epicor_Comm_Helper_Locations */

        $locEnabled = $locHelper->isLocationsEnabled();
        $skuResults = array();
        
        $html = '<ul>';
        
        $productCount = 0;
        foreach ($products as $product) {
            /* @var $product Epicor_Comm_Model_Product */
            $nameDisplay = $product->getCustomDescription() ?: $product->getName();
            $skuDisplay = $product->getSkuDisplay();
            $isConfigurator = $product->getConfigurator() ? $product->getConfigurator() : 2;
            $typeId = $product->getTypeId();
            $productCount++;
            if ($typeId == 'grouped') {
                $skuData = $product->getSkuDisplay();
                Mage::register('SkipEvent', true);
                $children = $product->getTypeInstance(true)->getAssociatedProducts($product);
                Mage::unregister('SkipEvent');
                
                $childArray = array();
                foreach ($children as $child) {
                    $childArray[$child->getEntityId()] = $child;
                }
                ksort($childArray);
                
                $contracts = '';
                $contractstring = '';


                foreach ($childArray as $child) {
                    /* @var $child Epicor_Comm_Model_Product */
                    $eccProduct = (strpos($child->getSku(), $helper->getUOMSeparator()) !== false);
                    $uomData = $eccProduct ? $child->getUom() : '';
                    $uomDisplay = $eccProduct ? $child->getUom() : $child->getName();
                    $childSkuData = $eccProduct ? $skuData : $child->getSku();
                    $childSkuDisplay = $eccProduct ? $skuData : $skuData . ' - ' . $child->getSku();

                    if ($this->_lastChildId >= $child->getEntityId()) {  // don't display the last shown child or previous children and remove from array
                        unset($childArray[$child->getEntityId()]);
                        continue;
                    }
                    
                    if ($contractHelper->contractsEnabled()) {
                        $contracts = $contractProductHelper->activeContractsForProduct($child->getId());
                        $contractString = is_array($contracts) ? base64_encode(json_encode($contracts)) : null;
                    }
                     $decimalPlaces = Mage::helper('epicor_comm')->getDecimalPlaces(Mage::getResourceModel('catalog/product')->getAttributeRawValue($child->getId(), 'decimal_places', Mage::app()->getStore()->getStoreId()));
                    $html .= '<li id="super_group_' . $child->getId()
                        . '" title="' . $this->htmlEscape($childSkuData)
                        . '" decimal="' . $decimalPlaces
                        . '" configurator="' . $isConfigurator
                        . '" class="' . ($x % 2 ? 'even' : 'odd') . ($x == 0 ? ' first' : '')
                        . '" data-uom="' . $uomData
                        . '" data-pack="' . $child->getPackSize()
                        . '" data-id="' . $child->getId()
                        //. '" data-contracts="' . $contractString
                        . '" data-parent="' . $product->getId() . '"'
                        . ($locEnabled ? ' data-locations="' . $this->getLocationsJson($child) . '"' : '') . '>'
                        . $this->htmlEscape($childSkuDisplay) . ' - ' . $this->htmlEscape($child->getName()) . ' - ' . $this->htmlEscape($child->getPackSize()) . '</li>';

                    $x++;

                    unset($childArray[$child->getEntityId()]);
                    $this->_lastChildId = !empty($childArray) ? $child->getEntityId() : 0; // check if any more children to display (array will not be empty)
                    
                    if (!empty($perPage) && $x > $maxX) {
                        $groupChildData = $childSkuData . "$$$" . $childSkuDisplay . "$$$" . $child->getName() . "$$$" . $child->getPackSize();
                        break;
                    }
                }
            } else {
                $this->_lastChildId = 0;
                if (!$product->getParentIds() || strpos($product->getSku(), $helper->getUOMSeparator()) === false) {
                    if ($contractHelper->contractsEnabled()) {
                        $contracts = $contractProductHelper->activeContractsForProduct($product->getId());
                        $contractString = is_array($contracts) ? base64_encode(json_encode($contracts)) : null;
                    }
                    $decimalPlaces = Mage::helper('epicor_comm')->getDecimalPlaces(Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'decimal_places', Mage::app()->getStore()->getStoreId()));
                    
                    $html .= '<li id="' . $product->getId()
                        . '" title="' . $this->htmlEscape($skuDisplay)
                        . '" configurator="' . $isConfigurator
                        . '" decimal="' . $decimalPlaces
                        . '" class="' . ($x % 2 ? 'even' : 'odd') . ($x == 0 ? ' first' : '') . '"'
                        //. '" data-contracts="' . $contractString  . '"'
                        . ($locEnabled ? ' data-locations="' . $this->getLocationsJson($product) . '"' : '')
                        . '>' . $this->htmlEscape($skuDisplay) . ' - ' . $this->htmlEscape($nameDisplay) . '</li>';
                    $x++;
                }
            }
            
            $this->_rowCount++;
            $rowId = $product->getId();
            if (!empty($perPage) && $x > $maxX) {
                break;
            }
        }

        if (!$this->_lastChildId) {
            if ($productCount >= count($products)) {  // if no more children check if there is another row, if so save row id
                $rowId = false;
            }
        }
        $this->_lastChildId ? $this->_rowCount-- : null;                    // if last entry displayed was child of a row stay on same row (as there might be more children), else go to next row
        
        
        $html.= '</ul>';

        $this->_pageData[$this->_page + 1] = array('row_count' => $this->_rowCount, 'last_child_id' => $this->_lastChildId);
        
        $html.= '<input type="hidden" id="qa_page_data" value="' . $helper->eccEncode(serialize($this->_pageData)) . '" />';

        if ($rowId) {
            $html.= '<button type="button" class="button qa_more" id="qa_next_btn" value="' . ($this->_page + 1) . '"><span>' . $this->__('Get More Results') . '</span></button>';
        }
        return $html;
    }

    public function getSuggestData()
    {
        if (!$this->_suggestData) {
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */            
            $locHelper = Mage::helper('epicor_comm/locations');
            /* @var $locHelper Epicor_Comm_Helper_Locations */
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            /* @var $connection Magento_Db_Adapter_Pdo_Mysql */
            
            $erpAccount = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $customerGroupId = ($erpAccount && !$erpAccount->isDefaultForStore()) ? $erpAccount->getId() : 0;
            $globalGroupId = ($erpAccount && !$erpAccount->isDefaultForStore()) ? ' OR `cpn`.`customer_group_id` = "0"' : '';
                        
            $sku = $this->getEnteredSku();
            $products = Mage::getModel('catalog/product')->getCollection();
            /* @var $products Mage_Catalog_Model_Resource_Product_Collection */
            $products->setStoreId(Mage::app()->getStore()->getId());
            $products->addAttributeToSelect(array('sku', 'name', 'type_id', 'configurator'));

            /* CPN JOIN */
            $cpnTable = $products->getTable('epicor_comm/customer_sku');
            $cpnCond = '`cpn`.`product_id` = `e`.`entity_id` 
                AND (`cpn`.`customer_group_id` = "' . $customerGroupId . '"' . $globalGroupId . ')
                AND `cpn`.`sku` LIKE(' . $connection->quote($sku . '%') . ')';
            $products->getSelect()->joinLeft(array('cpn' => $cpnTable), $cpnCond, array('sku_display' => new Zend_DB_Expr('IFNULL(cpn.sku, e.sku)'), 'custom_description' => 'description'));
            
            /* PRODUCT LINK */
            $linkTable = $products->getTable('catalog/product_link');
            $linkCond = '`link`.`linked_product_id` = `e`.`entity_id` 
                AND `link`.`link_type_id` = "' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED . '"';
            $products->getSelect()->joinLeft(array('link' => $linkTable), $linkCond, array('parent_ids' => 'product_id'));
            
            $products->getSelect()->limit(50, $this->_rowCount);
            $products->setVisibility(array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH, Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH));
            $products->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
            $products->getSelect()->where('(`e`.`sku` like (?) OR `cpn`.`sku` LIKE(?))', $sku.'%', $sku.'%');

            if ($locHelper->isLocationsEnabled()) {
                $locationCodes = $locHelper->getCustomerDisplayLocationCodes();

                $quotedLocations = array();
                foreach ($locationCodes as $locationCode) {
                    $quotedLocations[] = $connection->quote($locationCode);
                }

                if (empty($quotedLocations)) {
                    $quotedLocations[] = "''";
                }

                $locationString = implode(',', $quotedLocations);

                $locTable = $products->getTable('epicor_comm/location_product');
                $products->getSelect()->joinLeft(array('locations' => $locTable), 'locations.product_id = e.entity_id');
                $products->getSelect()->where('locations.location_code IN (' . $locationString . ')');
            }
            $products->getSelect()->order(new Zend_DB_Expr('IFNULL(cpn.sku, e.sku) asc'));
            $products->getSelect()->group('e.sku');
            $this->_suggestData = $products->getItems();
        }

        return $this->_suggestData;
    }
    
    public function getEnteredSku()
    {
        if (!$this->_enteredSku) {
            $this->_enteredSku = Mage::app()->getRequest()->getParam('sku');
        }
        
        return $this->_enteredSku;
    }

    /**
     * Processes a product and returns the locations as an encoded string
     * 
     * @param Epicor_Comm_Model_Product $product
     * 
     * @return string
     */
    protected function getLocationsJson($product)
    {
        $locArray = $product->getCustomerLocations();
        $locData = array();
        foreach ($locArray as $locationCode => $location) {
            /* @var $location Epicor_Comm_Model_Location_Product */
            $locData[] = array(
                'code' => $locationCode,
                'name' => $location->getName()
            );
        }
        return htmlspecialchars(json_encode($locData));
    }

}
