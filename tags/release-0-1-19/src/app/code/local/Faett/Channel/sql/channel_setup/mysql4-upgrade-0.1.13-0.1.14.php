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


$installer->setConfigData(
	Faett_Channel_Helper_Data::FAETT_CHANNEL_CRYPT_IV,
    substr(md5((string) Mage::getConfig()->getNode('global/crypt/key')), 0, 16)
);

$configValuesMap = array(
    Faett_Channel_Model_Mysql4_Link::XML_PATH_NEW_RELEASE_EMAIL_TEMPLATE =>
    'channel_new_release_email_template',
);

foreach ($configValuesMap as $configPath => $configValue) {
    $installer->setConfigData($configPath, $configValue);
}
$installer->run("

CREATE TABLE IF NOT EXISTS `{$installer->getTable('channel/link_update')}` (
  `link_update_id` int(10) unsigned NOT NULL auto_increment,
  `link_id_fk` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`link_update_id`),
  KEY `link_id` (`link_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$installer->getTable('channel/link_update')}` ADD FOREIGN KEY ( `link_id_fk` ) REFERENCES {$installer->getTable('package/link')} (`link_id`) ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('channel/subscription_type')}` (
  `subscription_type_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL DEFAULT '',
  `is_required` tinyint(4) NOT NULL DEFAULT '0',
  `type` enum('radio','select') NOT NULL default 'radio',
  PRIMARY KEY  (`subscription_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('channel/subscription_type_option')}` (
  `subscription_type_option_id` int(10) unsigned NOT NULL auto_increment,
  `subscription_type_id_fk` int(10) unsigned NOT NULL,
  `sku` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(12,4) NOT NULL default '0.0000',
  `price_type` enum('fixed','percent') NOT NULL default 'fixed',
  `value` int(10) NOT NULL DEFAULT '0',
  `sort_order` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`subscription_type_option_id`),
  KEY `IDX_SUBSCRIPTION_TYPE_ID_FK` (`subscription_type_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `{$installer->getTable('channel/subscription_type_option')}` ADD FOREIGN KEY ( `subscription_type_id_fk` ) REFERENCES {$installer->getTable('channel/subscription_type')} (`subscription_type_id`) ON DELETE CASCADE;

INSERT INTO `{$installer->getTable('channel/subscription_type')}` (`subscription_type_id`, `title`, `is_required`, `type`) VALUES
(1, 'Subscription', 1, 'radio'),
(2, 'Support', 0, 'radio');

INSERT INTO `{$installer->getTable('channel/subscription_type_option')}` (`subscription_type_option_id`, `subscription_type_id_fk`, `sku`, `title`, `price`, `price_type`, `value`, `sort_order`) VALUES
(1, 1, 'SUBSCRIPTION-01', 'Trial (30 days)', -100.0000, 'percent', 1, 1),
(2, 1, 'SUBSCRIPTION-02', '6 Month', 0.0000, 'fixed', 6, 2),
(3, 1, 'SUBSCRIPTION-03', '1 Year', 39.0000, 'fixed', 12, 3),
(4, 1, 'SUBSCRIPTION-04', '2 Years', 89.0000, 'fixed', 24, 4),
(5, 2, 'SUPPORT-01', '6 Month', 39.0000, 'fixed', 6, 1),
(6, 2, 'SUPPORT-02', '1 Year', 89.0000, 'fixed', 12, 2),
(7, 2, 'SUPPORT-03', '2 Years', 189.0000, 'fixed', 24, 3);

");

$installer->endSetup();