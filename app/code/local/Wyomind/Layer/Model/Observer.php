<?php
/**
 * Global module observer.
 *
 * @category    Wyomind
 * @package     Wyomind_Layer
 * @version     2.4.0
 * @copyright   Copyright (c) 2016 Wyomind (https://www.wyomind.com/)
 */
class Wyomind_Layer_Model_Observer
{
    /**
     * Clear cache when an attribute is saved
     */
    public function onAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $observer->getEvent()->getAttribute();
        $cacheIds = array(
            Wyomind_Layer_Helper_Data::ATTRIBUTE_LABELS_CACHE_ID,
            Wyomind_Layer_Helper_Data::OPTIONS_MAPPING_CACHE_PREFIX . $attribute->getAttributeCode(),
        );
        foreach ($cacheIds as $cacheId) {
            Mage::app()->getCache()->remove($cacheId);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function onCoreBlockToHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Layer_View) {
            /** @var Wyomind_Layer_Block_Catalog_Layer_View $block */

            // Fix bug: Unknown column 'cat_index_position' in 'order clause' and filters being removed in next pages
            $block->getLayer()
                ->getProductCollection()
                ->getSelect()
                ->reset(Zend_Db_Select::ORDER)
                ->reset(Zend_Db_Select::LIMIT_COUNT)
                ->reset(Zend_Db_Select::LIMIT_OFFSET);

            foreach ($block->getFilters() as $filter) {
                if (!$filter) {
                    continue;
                }
                $tpl = 'wyomind/layer/catalog/layer/filter.phtml';
                $helper = Mage::helper('layer');
                if ($helper->isCategoryTreeEnabled() && $helper->isCategoryFilter($filter->getFilter())) {
                    $tpl = 'wyomind/layer/catalog/layer/filter/category.phtml';
                } elseif ($helper->isPriceSliderEnabled() && $filter->getFilter()) {
                    if ($helper->isPriceFilter($filter->getFilter()) || $helper->isCustomPriceFilter($filter->getFilter())) {
                        $tpl = 'wyomind/layer/catalog/layer/filter/price.phtml';
                    }
                }

                $swatches = Mage::helper('core')->isModuleEnabled('Mage_ConfigurableSwatches');
                if (!$swatches || !$filter->getAttributeModel() ||
                    $filter->getAttributeModel()->getAttributeId() !=
                    Mage::helper('configurableswatches/productlist')->getSwatchAttributeId())
                {
                    /** @var Mage_Core_Block_Template $filter */
                    $filter->setTemplate($tpl);
                }
            }
        }

        // Handle meta robots value override
        if ($block instanceof Mage_Page_Block_Html_Head) {
            $filters = Mage::helper('layer/view')->getLayer()->getState()->getFilters();
            if (!empty($filters)) {
                $block->setData('robots', Mage::getStoreConfig('layer/general/meta_robots'));
            }
        }
    }

    /**
     * Display top layered navigation block
     *
     * @param Varien_Event_Observer $observer
     */
    public function onCoreBlockToHtmlAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();
        if ($block && in_array($block->getNameInLayout(), array('product_list', 'search_result_list'))) {
            $transport = $observer->getTransport();
            if ($transport) {
                $html = $transport->getHtml();
                $top = $block->getLayout()->createBlock('layer/catalog_layer_view', 'wyomind.layer.top')
                    ->setTemplate('wyomind/layer/catalog/layer/top.phtml');
                $transport->setHtml($top->toHtml() . $html);
            }
        }
    }

    /**
     * @return bool
     */
    public function handleAjaxLayer()
    {
        $request = Mage::app()->getRequest();
        if (!$request->isXmlHttpRequest()) {
            return false;
        }

        $layout         = Mage::app()->getLayout();
        $head           = $layout->getBlock('head');
        $contentBlock   = $request->has('q') ? 'search_result_list' : 'product_list';
        $content        = $layout->getBlock($contentBlock); // Careful! 'content' BEFORE 'left' is important
        $top            = $layout->getBlock('wyomind.layer.top');
        $left           = $layout->getBlock('left_first');
        $right          = $layout->getBlock('wyomind.layer.right');
        if (!$left) {
            $left = $layout->getBlock('left');
        }

        $output = array(
            'title'     => $head    ? $head->getTitle() : '',
            'content'   => $content ? Mage::getSingleton('core/url')->sessionUrlVar($content->toHtml()) : '',
            'top'       => $top     ? Mage::getSingleton('core/url')->sessionUrlVar($top->toHtml())     : '',
            'left'      => $left    ? Mage::getSingleton('core/url')->sessionUrlVar($left->toHtml())    : '',
            'right'     => $right   ? Mage::getSingleton('core/url')->sessionUrlVar($right->toHtml())   : '',
        );

        Mage::app()->getResponse()
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody(Mage::helper('core')->jsonEncode($output))
            ->sendResponse();
        exit;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function onProductCollectionApplyLimitationsAfter(Varien_Event_Observer $observer)
    {
        $categoryIds = Mage::registry('layer_applied_category_ids');
        if (!empty($categoryIds)) {
            $collection = $observer->getEvent()->getCollection();
            $select     = $collection->getSelect();

            if (!$collection->isEnabledFlat()) {
                $colsPart   = $select->getPart(Zend_Db_Select::COLUMNS);
                foreach ($colsPart as $k => $col) {
                    if (isset($col[2]) && $col[2] == 'cat_index_position') {
                        unset($colsPart[$k]);
                    }
                }
                $select->setPart(Zend_Db_Select::COLUMNS, $colsPart);
            }

            $fromPart = $select->getPart(Zend_Db_Select::FROM);

            if (isset($fromPart['cat_index'])) {
                unset($fromPart['cat_index']);
            }
            $select->setPart(Zend_Db_Select::FROM, $fromPart);

            $resource       = Mage::getResourceModel('catalog/category_indexer_product');
            $connection     = $resource->getReadConnection();
            $visibilityIds  = Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds();
            $conditions     = array(
                'e.entity_id = cat_index.product_id',
                $connection->quoteInto('cat_index.category_id IN (?)', $categoryIds),
                $connection->quoteInto('cat_index.visibility IN (?)', $visibilityIds),
                $connection->quoteInto('cat_index.store_id = ?', Mage::app()->getStore()->getId())
            );
            $select->join(
                array(
                    'cat_index' => $resource->getMainTable()),
                implode(' AND ', $conditions),
                array()
            );
            $select->group('e.entity_id');

            // Fix for Magento EE: Unknown column 'cat_index.category_id' in 'on clause'
            // This error is due to join clauses order, so we push permission clause to the end
            $fromPart = $select->getPart(Zend_Db_Select::FROM);
            if (isset($fromPart['permission_index_product'])) {
                $part = $fromPart['permission_index_product'];
                unset($fromPart['permission_index_product']);
                $fromPart['permission_index_product'] = $part;
                $select->setPart(Zend_Db_Select::FROM, $fromPart);
            }
        }
    }
}