<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author Paul.Ketelle
 */
class Epicor_ProductFeed_GenerateController extends Mage_Core_Controller_Front_Action 
{
    function googlefeedAction() {
        Epicor_ProductFeed_Model_Feed::googleFeed();
    }
}


