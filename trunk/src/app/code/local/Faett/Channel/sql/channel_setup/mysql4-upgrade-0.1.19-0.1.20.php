<?php

/**
 * Faett_Channel
 *
 * NOTICE OF LICENSE
 * 
 * Faett_Channel is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Faett_Channel is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Faett_Channel.  If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Faett_Channel to newer
 * versions in the future. If you wish to customize Faett_Channel for your
 * needs please refer to http://www.faett.net for more information.
 *
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 */

/**
 * @category   	Faett
 * @package    	Faett_Channel
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS `{$installer->getTable('channel/validation_request')}` (
  `validation_request_id` int(10) unsigned NOT NULL auto_increment,
  `link_purchased_id_fk` int(10) unsigned NOT NULL,
  `ip_address` varchar(15) NOT NULL default '',
  `validated` tinyint(1) unsigned NOT NULL default '0',
  `counter` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY  (`validation_request_id`),
  KEY `VALIDATION_REQUEST_IDX_01` (`link_purchased_id_fk`),
  KEY `VALIDATION_REQUEST_IDX_03` (`counter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$installer->getTable('channel/validation_request')}` ADD FOREIGN KEY ( `link_purchased_id_fk` ) REFERENCES {$installer->getTable('package/link_purchased')} (`purchased_id`) ON DELETE CASCADE;

");

$installer->endSetup();