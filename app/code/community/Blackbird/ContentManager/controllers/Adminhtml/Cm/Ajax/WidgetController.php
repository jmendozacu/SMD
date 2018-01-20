<?php


class Blackbird_ContentManager_Adminhtml_Cm_Ajax_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }
    
    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->getLayout()->createBlock(
                    'adminhtml/promo_widget_chooser_sku', 'promo_widget_chooser_sku',
                    array('js_form_object' => $request->getParam('form'),
                ));
                break;
            
            case 'content':
                $block = $this->getLayout()->createBlock(
                    'contentmanager/adminhtml_widget_chooser_content', 'widget_chooser_content',
                    array(
                        'js_form_object' => $request->getParam('form'),
                        'content_type' => $request->getParam('content_type'),
                ));
                break;
            
            case 'content_widget':
                $uniqId = $this->getRequest()->getParam('uniq_id');
                $block = $this->getLayout()->createBlock('contentmanager/adminhtml_widget_chooser_content_widget', '', array(
                    'id' => $uniqId,
                ));
                
                break;          
            
            case 'category':
                $block = $this->getLayout()->createBlock(
                    'contentmanager/adminhtml_widget_chooser_category', 'widget_chooser_category',
                    array(
                        'js_form_object' => $request->getParam('form')
                ));
                break;
            
            case 'pag':
                $block = $this->getLayout()->createBlock(
                    'contentmanager/adminhtml_widget_chooser_page', 'widget_chooser_page',
                    array('js_form_object' => $request->getParam('form'),
                ));
                break;
            
            case 'block':
                $block = $this->getLayout()->createBlock(
                    'contentmanager/adminhtml_widget_chooser_block', 'widget_chooser_block',
                    array('js_form_object' => $request->getParam('form'),
                ));
                break;

            case 'category_ids':
                $ids = $request->getParam('selected', array());
                if (is_array($ids)) {
                    foreach ($ids as $key => &$id) {
                        $id = (int) $id;
                        if ($id <= 0) {
                            unset($ids[$key]);
                        }
                    }

                    $ids = array_unique($ids);
                } else {
                    $ids = array();
                }


                $block = $this->getLayout()->createBlock(
                        'adminhtml/catalog_category_checkboxes_tree', 'promo_widget_chooser_category_ids',
                        array('js_form_object' => $request->getParam('form'))
                    )
                    ->setCategoryIds($ids)
                ;
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Initialize category object in registry
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');

        $category   = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                    return false;
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);

        return $category;
    }
}
