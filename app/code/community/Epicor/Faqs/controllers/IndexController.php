<?php

/**
 * Faqs frontend controller
 * @category   Epicor
 * @package    Faq
 * @author     Epicor Websales Team
 * 
 */
class Epicor_Faqs_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Pre dispatch action that allows to redirect to no route page in case of disabled extension through admin panel
     */
    public function preDispatch() {
        parent::preDispatch();

        if (!Mage::helper('epicor_faqs')->isEnabled()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }
    }

    /**
     * Index action
     * 
     * Sets up the necessary blocks, stylesheets and javascripts depending on
     * wether the accordion or paragraphs view was configured in the admin
     * config F.A.Q. panel.
     */
    public function indexAction() {
        $this->loadLayout();
        $layout = $this->getLayout();
        /**
         * Adds the necessary Javascript if the the front-end view is set to accordion
         * Otherwise adds the index block for the indexed paragraphs view
         */
        $layout->getBlock('head')->setTitle($this->__('F.A.Q.'));

        $presentation = Mage::helper('epicor_faqs')->getPresentation();
        if ($presentation == 'accordion') {
            $layout->getBlock('head')
                    ->addItem('skin_js', 'epicor/faqs/js/createAccordion.js');
        }

        $this->renderLayout();
    }

    /**
     * voteAjax action
     * 
     * 1. Checks wether the user has already submitted a vote for the voted F.A.Q.
     * 
     * 2a. If the user had never submitted a vote for the selected F.A.Q.: 
     *  i.Register the vote in the epicor_faqs_votes table
     *  ii.Add the vote value to the corresponding column in the epicor_faqs table.
     * 
     * 2b.If the user has previously submitted a vote for the selected F.A.Q., but
     *  the submitted value is different from the existing one:
     *  i.Modify the vote value in the epicor_faqs_votes table
     *  ii.Modify the corresponding columns in epicor_faqs
     */
    public function submitVoteAction() {
        $msg = "";
        $post = $this->getRequest();
        if (empty($post)) {
            $msg = "Request Error";
        } else {
            $model_vote = Mage::getModel('epicor_faqs/vote');
            $voted_registered = false;
            $changed_vote = false;

            $collection = $model_vote->getCollection()->addFilter('faqs_id', $post->getParam('faqId'))->addFilter('customer_id', Mage::helper('epicor_faqs')->getUserId());
            if ($collection->getSize() > 0) {
                $model_vote->setId($collection->getFirstItem()->getId());
                $voted_registered = true;
                $changed_vote = $collection->getFirstItem()->getValue() != $post->getParam('vote');
            }

            $model_vote->setFaqsId($post->getParam('faqId'));
            $model_vote->setCustomerId(Mage::helper('epicor_faqs')->getUserId());
            $model_vote->setValue($post->getParam('vote'));
            $model_vote->save();

            if (!$voted_registered || $changed_vote) {
                $model_faq = Mage::getModel('epicor_faqs/faqs');
                $model_faq->load($post->getParam('faqId'));
                if ($changed_vote) {
                    if ($post->getParam('vote') > 0) {
                        if ($model_faq->getUseless() > 0) {
                            $model_faq->setUseless($model_faq->getUseless() - 1);
                        }
                    } else {
                        if ($model_faq->getUseful() > 0) {
                            $model_faq->setUseful($model_faq->getUseful() - 1);
                        }
                    }
                }
                if ($post->getParam('vote') > 0) {
                    $model_faq->setUseful($model_faq->getUseful() + 1);
                } else {
                    $model_faq->setUseless($model_faq->getUseless() + 1);
                }
                $model_faq->save();
            }

            $msg = "Voted!";
        }
        //This is the tricky part for the output of a json message to the browser   

        $this->_redirectUrl(Mage::getUrl('epicor_faqs/index/vote', array('id' => $post->getParam('faqId'))));
    }

    function voteAction(){	
		$this->loadLayout();
		$faqId = $this->getRequest()->getParam('id');
		$this->getLayout()->getBlock('root')->setFaqId($faqId);
		$this->getLayout()->removeOutputBlock('core_profiler');
		$this->renderLayout();
	}

}
