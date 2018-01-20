<?php

/**
 * Copyright (c) 2016, SILK Software
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
 *   names of its contributors may be used to endorse or promote products
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
 * Created by PhpStorm.
 * User: Bob song <bob.song@silksoftware.com>
 * Date: 17-3-1
 * Time: 11:16
 */
class Silk_Retailer_Adminhtml_RetailersController extends Mage_Adminhtml_Controller_Action
{
	protected function _construct()
	{
		$this->setUsedModuleName('silk_retailer');
	}

	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	protected function editAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	protected function newAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * save retailer
	 * @return bool
	 */
	public function saveAction()
	{
		try {
			$params = $this->getRequest()->getParams();
			if ($params['id']) {
				$model = Mage::getModel('silk_retailer/retailer')->load($params['id']);
				$this->setDataAction($model, $params);
				$this->_getSession()->addSuccess($this->__('Update success'));
			} else {
				$model = Mage::getModel('silk_retailer/retailer');
				$this->setDataAction($model, $params);
				$model->save();
				$this->_getSession()->addSuccess($this->__('Add success'));
			}
			$this->_redirect('*/*/');
		} catch (Exception $e) {
			$this->_redirect('*/*/');
			return false;
		}
	}

	/**
	 * @param $model
	 * @param $params
	 */
	protected function setDataAction($model, $params)
	{
		$model->setData($params);
		$model->save();
	}

	/**
	 * export excel
	 * @return bool
	 */
	public function exportExcelAction()
	{
		$ids = (array)$this->getRequest()->getParam('id');
		foreach ($ids as $id) {
			$model = Mage::getModel('silk_retailer/retailer')->load($id)->getdata();
			$retailers[] = $model;
		}
		$titleArr = array_keys($retailers[0]);

		try {
			$path = Mage::getBaseDir('var') . DS . 'export' . DS;
			$name = md5(microtime());
			//$file = $path . DS . $name . '.xlsx';
			$file = $path . DS . $name . '.csv';
			//$fileName = 'retailer.xlsx';
			$header = $titleArr;

			//$parser = new Varien_Convert_Parser_Xml_Excel();
			$io = new Varien_Io_File();

			$io->setAllowCreateFolders(true);
			$io->open(array('path' => $path));
			$io->streamOpen($file, 'w+');
			$io->streamLock(true);
			//$io->streamWrite($parser->getHeaderXml($fileName));
			//$io->streamWrite($parser->getRowXml($header));
			$io->streamWriteCsv($header);
			
			foreach ($retailers as $item) {
				//$row = $item;
				//$io->streamWrite($parser->getRowXml($row));
				$io->streamWriteCsv($item);
			}

			//$io->streamWrite($parser->getFooterXml());
			$io->streamUnlock();
			$io->streamClose();

			$excel = array(
				'type'  => 'filename',
				'value' => $file,
				'rm'    => true // can delete file after use
			);

			//$this->_prepareDownloadResponse('retailer.xlsx', $excel);
			$this->_prepareDownloadResponse('retailer.csv', $excel);
		} catch (Exception $e) {
			$this->_redirect('*/*/');
			return false;
		}
		exit;
	}

	public function deleteAction()
	{
		$params = $this->getRequest()->getParams();
		if (isset($params['id'])) {
			$id = (int)$params['id'];
			Mage::getModel('silk_retailer/retailer')->load($id)->delete();
			$this->_getSession()->addNotice('Delete Success.');
		}

		$this->_redirect('silk_retailer/adminhtml_retailers/index');
	}

}
