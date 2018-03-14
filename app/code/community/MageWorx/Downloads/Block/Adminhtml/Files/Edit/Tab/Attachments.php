<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Tab_Attachments
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_categoryIds;
    protected $_withFilesCount;
    protected $_selectedNodes = null;
    protected $_productId;
    protected $_product = null;

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('mageworx/downloads/files_edit_tab_attachments.phtml');
        $this->_withFilesCount = true;
        $this->_productId = (int)$this->getRequest()->getParam('id');
    }

    public function getTitleHtml()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject($this->getProduct());
        $title = $form->addField(
            'downloads_title', 'text', array(
            'label' => $this->_getHelper()->__('Title'),
            'title' => $this->_getHelper()->__('Title'),
            'name' => 'product[downloads_title]',
            'value' => $this->getProduct()->getDownloadsTitle(),
            )
        );
        $title->setEntityAttribute($this->getProduct()->getResource()->getAttribute('downloads_title'));
        $title->setRenderer($this->getLayout()->createBlock('adminhtml/catalog_form_renderer_fieldset_element'));

        return $title->toHtml();
    }

    protected function getFilesOfProductIds()
    {
        $files = Mage::registry('product_files');
        if (null === $files) {
            $files = (array)Mage::getResourceSingleton('mageworx_downloads/relation')->getFileIds($this->_productId);
            Mage::register('product_files', $files);
        }
        return $files;
    }

    protected function getRelationCategories()
    {
        $relation = Mage::registry('relation_categories');
        if (null === $relation) {
            $relation = (array)Mage::getResourceModel('mageworx_downloads/relation')->getCategoryIds($this->_productId);
            Mage::register('relation_categories', $relation);
        }
        return $relation;
    }

    protected function _getStoreId()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(true)->getId();
        }
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId)->getId();
    }

    protected function getRootCategories()
    {
        $items = Mage::registry('root_categories');
        if (null === $items) {
            $categories = Mage::getResourceSingleton('mageworx_downloads/categories_collection')
                ->addSortOrder();

            $items = $categories->getItems();
            Mage::register('root_categories', $items);
        }
        return $items;
    }

    public function getIdsString()
    {
        return implode(',', $this->getFilesOfProductIds());
    }

    protected function _getNodeJson($node, $level = 1)
    {
        $item = array();
        $cssIcon = 'leaf';
        if ($level == 1) {
            $nodeId = $node->getCategoryId();
            $cssIcon = 'folder';
            $item['isParent'] = true;
            $filesCount = Mage::getResourceSingleton('mageworx_downloads/files')->getCountFiles($nodeId);
            if ($filesCount > 0) {
                $node->setFilesCount($filesCount);
                $item['children'] = array();

                if (in_array($nodeId, $this->getRelationCategories())) {
                    $item['expanded'] = true;
                }
            }
        } else {
            $nodeId = $node->getFileId();
            if (in_array($nodeId, $this->getFilesOfProductIds())) {
                $item['checked'] = true;
            }
        }

        if ($node->getIsActive() == MageWorx_Downloads_Helper_Data::STATUS_ENABLED) {
            $catStatusClass = 'active-category';
        } else {
            $catStatusClass = 'no-active-category';
        }

        $item['id'] = $nodeId;
        $item['text'] = $this->buildNodeName($node);
        $item['cls'] = $cssIcon . ' ' . $catStatusClass;
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;

        return $item;
    }

    public function buildNodeName($node)
    {
        $name = $node->getTitle();
        if (empty($name)) {
            $name = $node->getName() . ' (' . $node->getType() . ')';
        }
        $result = $this->htmlEscape(trim($name));
        if ($this->_withFilesCount && $node->getFilesCount()) {
            $result .= ' (' . $node->getFilesCount() . ')';
        }
        return $result;
    }

    public function getTreeJson()
    {
        $items = $this->getRootCategories();
        $rootArray = array();
        if ($items) {
            foreach ($items as $item) {
                $rootArray[] = $this->_getNodeJson($item);
            }
        }
        return Zend_Json::encode($rootArray);
    }

    public function getCategoryChildrenJson($categoryId)
    {
        $categoryFiles = Mage::getResourceSingleton('mageworx_downloads/files')->getCategoryFiles($categoryId);
        if (!$categoryFiles) {
            return '[]';
        }

        $children = array();
        foreach ($categoryFiles as $file) {
            $children[] = $this->_getNodeJson(new Varien_Object($file), 2);
        }
        return Zend_Json::encode($children);
    }

    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('adminhtml/mageworx_downloads_files/categoriesJson', array('_current' => true));
    }

    protected function _getHelper()
    {
        return Mage::helper('mageworx_downloads');
    }

    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return $this->_getHelper()->__('Attachments');
    }

    public function getTabTitle()
    {
        return $this->_getHelper()->__('Attachments');
    }

    public function getAfter()
    {
        return 'categories';
    }

    public function canShowTab()
    {
        $request = Mage::app()->getRequest();
        $productSet = $request->getParam('set');
        if ($request->getControllerName() == 'catalog_product' && $request->getActionName() == 'new' && !isset($productSet)) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getUploadForm()
    {
        $helper = Mage::helper('mageworx_downloads');
        $form = new Varien_Data_Form();
        $form->addField(
            'category_id', 'select', array(
            'label' => $helper->__('Category'),
            'name' => 'fd_file[category_id]',
            'values' => Mage::getSingleton('mageworx_downloads/categories')->getCategoriesList(),
            'required' => true
            )
        );

        $form->addField(
            'file_description', 'textarea', array(
            'label' => $helper->__('Description'),
            'name' => 'fd_file[file_description]',
            'index' => 'file_description',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $form->addField(
                'store_ids', 'multiselect', array(
                'label' => $helper->__('Stores'),
                'name' => 'fd_file[store_ids]',
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                )
            );
        } else {
            $form->addField(
                'store_id', 'hidden', array(
                'name' => 'fd_file[store_ids]',
                'value' => Mage::app()->getStore(true)->getId(),
                )
            );
        }

        $customerGroups = $this->_getCustomerGroups();
        if ($customerGroups) {
            $form->addField(
                'customer_groups', 'multiselect', array(
                'label' => $helper->__('Customer Groups'),
                'name' => 'fd_file[customer_groups][]',
                'values' => $customerGroups,
                )
            );
        }

        $multiUpload = array(
            'label' => $helper->__('Multi Upload'),
            'name' => 'general[multiupload]',
            'index' => 'multiupload',
            'values' => uniqid()
        );

        $form->addType('multiupload', Mage::getConfig()->getGroupedClassName('model', 'mageworx_downloads/form_element_multiupload'));
        $form->addField('multiupload', 'multiupload', $multiUpload);

        $form->addField(
            'url', 'text', array(
            'label' => $helper->__('URL'),
            'name' => 'fd_file[url]',
            'index' => 'url',
            )
        );

        $form->addField(
            'embed_code', 'textarea', array(
            'label' => $helper->__('Embedded Video Code'),
            'name' => 'fd_file[embed_code]',
            'required' => false
            )
        );

        return $form->toHtml();
    }

    protected function _getCustomerGroups()
    {
        $result = array();
        $customerGroups = Mage::getSingleton('customer/group')->getCollection()->getItems();
        if ($customerGroups) {
            foreach ($customerGroups as $item) {
                $result[] = array(
                    'value' => $item->getData('customer_group_id'),
                    'label' => $item->getData('customer_group_code')
                );
            }
        }
        return $result;
    }
}