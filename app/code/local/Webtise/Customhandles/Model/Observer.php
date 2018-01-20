<?php
    class Webtise_Customhandles_Model_Observer
    {
        /**
         * Add Page Layout handles to Category/CMS/Product dependant on registry object
         * @param $observer
         */
        public function addRootLayoutHandles($observer){
            $layout = $observer->getEvent()->getLayout();
            $page = $this->getCurrentPage();
            if($page && $page->getPageLayout() != ""){
                $layout->getUpdate()->addHandle($page->getPageLayout());
            }
        }

        /**
         * Return the current registry for Category/CMS/Product
         * @return mixed
         */
        public function getCurrentPage()
        {
            if($product = Mage::registry('current_product')) {
                return $product;
            } elseif($category = Mage::registry('current_category')){
                return $category;
            } elseif($cms = Mage::registry('cms_page')) {
                return $cms;
            }
            return false;
        }
    }