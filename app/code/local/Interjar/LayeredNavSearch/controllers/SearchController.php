<?php
/**
 * Created by PhpStorm.
 * User: joshuacarter
 * Date: 12/12/2017
 * Time: 16:51
 */
class Interjar_LayeredNavSearch_SearchController extends Mage_Core_Controller_Front_Action
{
    /**
     * Redirect Refer
     *
     * @return $this
     */
    public function indexAction()
    {
        $searchTerm = $this->getRequest()->getPost('st');
        if ($searchTerm) {
            $refererUrl = $this->_getRefererUrl() . '?st=' . $searchTerm;
            $this->getResponse()->setRedirect($refererUrl);
            return $this;
        }
        $this->_redirectReferer();
    }
}
