<?php
/**
 * Copyright (c) 2017, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @authors daniel (daniel.luo@silksoftware.com)
 * @date    17-3-6 下午1:38
 * @version 0.1.0
 */
class Silk_Retailer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
//        Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }

    /**
     * Get relaiter address
     */
    public function nearSearchAction()
    {
        $params = $this->getRequest()->getParams();
        $lat = $params['lat'];
        $lng = $params['lng'];
//        $radius = $params['radius'];
        $radius = Mage::getStoreConfig('google/map/radius', Mage::app()->getStore()->getStoreId());
        $keyWords = $params['addressInput'];
        $limitRadius = $this->getRequest()->getParam('hasRadius');

        $collection = Mage::getModel('silk_retailer/retailer')->getCollection();
        $connection = $collection->getResource()->getReadConnection();

        $binds = array(
            ':lat1' => $lat,
            ':lng1' => $lng,
            ':lat2' => $lat
        );

        if ($limitRadius != 'false') {
            $sql = "
SELECT retailer_id AS id, title AS name, address1 AS address, address2, town, email, city, country, state, zip, telephone, latitude AS lat, longtitude AS lng,
ROUND(( 3959 * acos( cos( radians(:lat1) ) * cos( radians( latitude ) ) * cos( radians( longtitude ) - radians(:lng1) ) + sin( radians(:lat2) ) * sin( radians( latitude ) ) ) ), 2) AS distance
FROM
silk_retailer
WHERE status=1
HAVING distance < :radius ORDER BY distance LIMIT 0 , 8;
";
            $binds[':radius'] = $radius;
//            $binds[':maxNum'] = Mage::getStoreConfig('google/map/map_marker', Mage::app()->getStore()->getStoreId());
        } else {
            $sql = "
SELECT retailer_id AS id, title AS name, address1 AS address, address2, town, email, city, country, state, zip, telephone, latitude AS lat, longtitude AS lng,
ROUND(( 3959 * acos( cos( radians(:lat1) ) * cos( radians( latitude ) ) * cos( radians( longtitude ) - radians(:lng1) ) + sin( radians(:lat2) ) * sin( radians( latitude ) ) ) ), 2) AS distance
FROM
silk_retailer
WHERE status=1
ORDER BY distance LIMIT 0 , 5;
";
//            $binds[':maxNum'] = Mage::getStoreConfig('google/map/retailer_number', Mage::app()->getStore()->getStoreId());
        }

        $query = $connection->query($sql, $binds);
        $result = $query->fetchAll();
/**
        $result = array(
            array(
                'name' => 'Tutta Bella Neapolitan Pizzera',
                'address' => '4918 Rainier Ave S, Seattle, WA',
                'lat' => 47.557705,
                'lng' => -122.284988,
                'distance' => 0.018554807321895135
            ),
            array(
                'name' => 'Piecora\'s New York Pizza',
                'address' => '1425 NW Glisan St, Portland, OR',
                'lat' => 47.614006,
                'lng' => -122.313988,
                'distance' => 0.028554807321895135
            ),
            array(
                'name' => 'Pagliacci Pizza',
                'address' => '550 Queen Anne Ave N, Seattle, WA',
                'lat' => 47.623943,
                'lng' => -122.35672,
                'distance' => 0.022554807321895135
            ),
        );
 //*/
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}