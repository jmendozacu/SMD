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
 * Time: 10:12
 */

$installer = $this;

$installer->startSetup();

$sql = <<<EOD
CREATE TABLE `{$installer->getTable('silk_retailer/silk_retailer_table')}` (
`id`  int(10) NOT NULL AUTO_INCREMENT ,
`retailer_id`  int(10) NOT NULL ,
`title`  varchar(100) NOT NULL ,
`address1`  varchar(150) NOT NULL ,
`address2`  varchar(150) NULL ,
`town`  varchar(150) NOT NULL ,
`email`  varchar(100) NULL ,
`city`  varchar(50) NOT NULL ,
`country`  varchar(50) NOT NULL ,
`state`  varchar(50) NOT NULL ,
`zip`  varchar(50) NOT NULL ,
`telephone`  varchar(20) NOT NULL ,
`latitude`  varchar(255) NULL ,
`longtitude`  varchar(255) NULL ,
`agent`  varchar(255) NULL ,
`stock`  varchar(255) NULL ,
`status`  tinyint(4) NULL DEFAULT 1 ,
`username`  varchar(100) NULL ,
`password`  varchar(50) NULL ,
PRIMARY KEY (`id`)
);
EOD;
$installer->run($sql);

$installer->endSetup();



