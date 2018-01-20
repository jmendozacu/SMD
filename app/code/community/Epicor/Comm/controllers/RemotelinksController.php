<?php

/**
 * Returns controller
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_ReturnsController extends Mage_Core_Controller_Front_Action
{

    public function fetchAction()
    {
        $data = $this->getRequest()->getParam('url');
        $content = '';
        $responseCode = 404;
        if ($data) {

            $content = 'Hello';
            $contentType = '';
            $contentLength = '';
            $fileName = '';
            $responseCode = 200;
            
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
		curl_setopt($ch, CURLOPT_POST, false);
		$content = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		curl_close($ch);
        }

        $this->getResponse()
                ->setHttpResponseCode($responseCode)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength, true)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
                ->setHeader('Last-Modified', date('r'), true)
                ->setBody($content);

        return $this;
    }

}
