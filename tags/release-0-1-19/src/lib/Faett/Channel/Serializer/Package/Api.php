<?php

/**
 * Faett_Channel_Serializer_Release_Package
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
 * @category   Faett
 * @package    Faett_Channel
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@faett.net>
 */
class Faett_Channel_Serializer_Package_Api
    extends Faett_Channel_Serializer_Release_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Faett/Channel/Serializer/Interfaces/Faett_Channel_Serializer_Interfaces_Serializer#serialize()
     */
    public function serialize()
    {
        try {
            // initialize a new DOM document
            $doc = new DOMDocument('1.0', 'UTF-8');
            // create new namespaced root element
            $config = $doc->createElement('config');

            $api = $doc->createElement('api');

            $this->_resources($doc, $api);
            $this->_acl($doc, $api);

            $config->appendChild($api);

            $doc->appendChild($config);

            // Mage::log(var_export($doc->saveXML(), true));

            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            Mage::logException($e);
            return $e->getMessage();
        }
    }

    protected function _resources($doc, $api)
    {
        $resources = $doc->createElement('resources');
            $r = $doc->createElement('r');
                $version = $doc->createElement('version');
                    $packageName = $doc->createElement($name = strtolower($this->_package->getPackageName()));
                    $packageName->setAttribute('translate', 'title');
                    $packageName->setAttribute('module', 'channel');
                        $title = $doc->createElement('title');
                        $title->nodeValue = $this->_package->getPackageName();
                        $model = $doc->createElement('model');
                        $model->nodeValue = 'channel/serializer_release';
                        $methods = $doc->createElement('methods');

                            $view = $doc->createElement('view');
                            $view->setAttribute('translate', 'title');
                            $view->setAttribute('module', 'channel');
                                $titleInfo = $doc->createElement('title');
                                $titleInfo->nodeValue = 'View';
                                $acl = $doc->createElement('acl');
                                $acl->nodeValue = 'channel/r/version/view';
                            $view->appendChild($acl);
                            $view->appendChild($titleInfo);

                            $download = $doc->createElement('download');
                            $download->setAttribute('translate', 'title');
                            $download->setAttribute('module', 'channel');
                                $titleInfo = $doc->createElement('title');
                                $titleInfo->nodeValue = 'Download';
                                $acl = $doc->createElement('acl');
                                $acl->nodeValue = 'channel/r/version/download';
                            $download->appendChild($acl);
                            $download->appendChild($titleInfo);

                        $methods->appendChild($view);
                        $methods->appendChild($download);
                    $packageName->appendChild($methods);
                    $packageName->appendChild($model);
                    $packageName->appendChild($title);
                $version->appendChild($packageName);
            $r->appendChild($version);
        $resources->appendChild($r);

        $api->appendChild($resources);
    }

    protected function _acl($doc, $api)
    {
        $acl = $doc->createElement('acl');
            $resources = $doc->createElement('resources');
                $channel = $doc->createElement('channel');
                    $r = $doc->createElement('r');
                        $version = $doc->createElement('version');
                            $packageName = $doc->createElement(strtolower($this->_package->getPackageName()));
                            $packageName->setAttribute('translate', 'title');
                            $packageName->setAttribute('module', 'channel');
                                $title = $doc->createElement('title');
                                $title->nodeValue = $this->_package->getPackageName();

                                $view = $doc->createElement('view');
                                $view->setAttribute('translate', 'title');
                                $view->setAttribute('module', 'channel');
                                    $titleInfo = $doc->createElement('title');
                                    $titleInfo->nodeValue = 'View';
                                $view->appendChild($titleInfo);

                                $download = $doc->createElement('download');
                                $download->setAttribute('translate', 'title');
                                $download->setAttribute('module', 'channel');
                                    $titleInfo = $doc->createElement('title');
                                    $titleInfo->nodeValue = 'Download';
                                $download->appendChild($titleInfo);

                            $packageName->appendChild($title);
                            $packageName->appendChild($view);
                            $packageName->appendChild($download);
                        $version->appendChild($packageName);
                    $r->appendChild($version);
                $channel->appendChild($r);
            $resources->appendChild($channel);
        $acl->appendChild($resources);

        $api->appendChild($acl);
    }
}