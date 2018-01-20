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
 * Date: 17-3-6
 * Time: 14:27
 */
class Silk_Retailer_Model_Retailer_Import extends Mage_ImportExport_Model_Import_Entity_Abstract
{
	/**
	 * @param null $retailerId
	 * @return false|Mage_Core_Model_Abstract
	 */
	public function getRetailerModel($retailerId = null)
	{
		if ($retailerId) {
			return Mage::getModel('silk_retailer/retailer')->loadByRetailerId($retailerId);
		} else {
			return Mage::getModel('silk_retailer/retailer');
		}

	}

	/**
	 * Import data rows.
	 *
	 * @return boolean
	 */
	protected function _importData()
	{
		while ($bunch = $this->_dataSourceModel->getNextBunch()) {
			foreach ($bunch as $rowNum => $rowData) {
				$retailerId = Mage::helper('silk_retailer')->getHash($rowData['city'], $rowData['state'], $rowData['zip'], $rowData['town']);
				// create or update
				if ($this->getRetailerModel($retailerId)->getId()) {
//					$this->setDataAction($model,$rowData);
				} else {
					// create
					$model = $this->getRetailerModel();
					$this->setDataAction($model,$rowData);
				}
			}
		}
		return true;
	}

	/**
	 * EAV entity type code getter.
	 *
	 * @return string
	 */
	public function getEntityTypeCode()
	{
		return 'silk_retailer_retailer_import';
	}

	/**
	 * Validate data row.
	 *
	 * @param array $rowData
	 * @param int $rowNum
	 * @return boolean
	 */
	public function validateRow(array $rowData, $rowNum)
	{
		$town = trim($rowData['town']);
		if (empty($town)) {
			$this->addRowError('town is required',$rowNum);
			return false;
		}

		$field = trim($rowData['city']);
		if (empty($field)) {
			$this->addRowError('city is required',$rowNum);
			return false;
		}

		$field = trim($rowData['state']);
		if (empty($field)) {
			$this->addRowError('state is required',$rowNum);
			return false;
		}

		$field = trim($rowData['zip']);
		if (empty($field)) {
			$this->addRowError('zip is required',$rowNum);
			return false;
		}

		$email = trim($rowData['email']);
		if (empty($email)) {
			$this->addRowError('email is required',$rowNum);
			return false;
		}

		$latitude = trim($rowData['latitude']);
		if (empty($latitude)) {
			$this->addRowError('latitude is required',$rowNum);
			return false;
		}

		$longtitude = trim($rowData['longtitude']);
		if (empty($longtitude)) {
			$this->addRowError('longtitude is required',$rowNum);
			return false;
		}

		return true;
	}

	/**
	 * @param $model
	 * @param $rowData
	 */
	protected function setDataAction($model, $rowData)
	{
		$model->setData($rowData);
		$model->save();
	}
}