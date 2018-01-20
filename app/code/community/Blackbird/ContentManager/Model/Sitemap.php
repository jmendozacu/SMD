<?php

class Blackbird_ContentManager_Model_Sitemap extends Mage_Sitemap_Model_Sitemap
{
    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        try {
           
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));

        if ($io->fileExists($this->getSitemapFilename()) && !$io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $storeId = $this->getStoreId();
        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate categories sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        /**
         * Generate products sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        /**
         * Generate CONTENT TYPE pages sitemap
         */
        $contentTypes = Mage::getModel('contentmanager/contenttype')
                ->getCollection()
                ->addFieldToFilter('sitemap_enable', 1);
        
        foreach($contentTypes as $contentType)
        {
            //get all contents
            $collection = Mage::getModel('contentmanager/content')
                    ->getCollection()
                    ->addStoreFilter($storeId)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToFilter('ct_id', $contentType->getId())
                    ->addAttributeToSelect('url_key');
            
            $changefreq = $contentType->getSitemapFrequency();
            $priority   = $contentType->getSitemapPriority();
            
            foreach($collection as $item) {
                $xml = sprintf(
                    '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%s</priority></url>',
                    htmlspecialchars($baseUrl . $item->getUrlKey()),
                    $date,
                    $changefreq,
                    $priority
                );
                $io->streamWrite($xml);
            }
            unset($collection);
        }
        

        /**
         * Generate MENU nodes sitemap
         */
        $menus = Mage::getModel('contentmanager/menu')
                ->getCollection()
                ->addStoreFilter($storeId)
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('sitemap_enable', 1);
        
        foreach($menus as $menu)
        {
            //get all contents
            $collection = $menu->getNodes(1);
            
            foreach($collection as $item) {
                $url = $item->getUrlPath();
                if(!$url && $item->getType() == 'content')
                {
                    $content = Mage::getModel('contentmanager/content')->getCollection()->addStoreFilter($storeId)->addAttributeToSelect('url_key')->addAttributeToFilter('entity_id', $item->getEntityId());
                    $url = Mage::getUrl($content->getFirstItem()->getUrlKey(), array('_direct' => $content->getFirstItem()->getUrlKey(), '_store' => $storeId));
                }
                elseif($url)
                {
                    $url = $baseUrl . $url;
                }
                
                switch($item->getLevel())
                {
                    case '0': 
                        $changefreq = $menu->getSitemapFrequency();
                        $priority   = $menu->getSitemapPriority();
                        break;
                    case '1': 
                        $changefreq = $menu->getSitemapFrequencyLevel1();
                        $priority   = $menu->getSitemapPriorityLevel1();
                        break;
                    case '2': 
                        $changefreq = $menu->getSitemapFrequencyLevel2();
                        $priority   = $menu->getSitemapPriorityLevel2();
                        break;
                    case '3': 
                        $changefreq = $menu->getSitemapFrequencyLevel3();
                        $priority   = $menu->getSitemapPriorityLevel3();
                        break;
                    case '4': 
                        $changefreq = $menu->getSitemapFrequencyLevel4();
                        $priority   = $menu->getSitemapPriorityLevel4();
                        break;
                    default: 
                        $changefreq = $menu->getSitemapFrequencyLevel4();
                        $priority   = $menu->getSitemapPriorityLevel4();
                        break;
                }
                
                if($url)
                {
                    $xml = sprintf(
                        '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%s</priority></url>',
                        htmlspecialchars($url),
                        $date,
                        $changefreq,
                        $priority
                    );
                    $io->streamWrite($xml);
                }
            }
            unset($collection);
        }
        
        /**
         * Generate cms pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority   = (string)Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        foreach ($collection as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $item->getUrl()),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
        }
        unset($collection);

        $io->streamWrite('</urlset>');
        $io->streamClose();

        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
         
        } catch (Exception $ex) {
            Mage::printException($ex); die;
        }
    }
}
