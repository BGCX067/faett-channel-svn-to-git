<?php

/**
 * Faett_Channel_Serializer_Release_Version
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
class Faett_Channel_Serializer_Release_Version
    extends Faett_Channel_Serializer_Release_Abstract {

    /**
     * Passes the package the serializer has to be attached.
     *
     * @param Mage_Api_Model_User $user
     * 		The user logged into the system
     * @param Faett_Channel_Model_Package $package
     * 		The package to serializer has to be attached to
     * @return void
     */
    public function __construct(
        Mage_Api_Model_User $user,
        Faett_Channel_Model_Package $package) {
        // set the passed package
        Faett_Channel_Serializer_Release_Abstract::__construct(
            $user,
            $package
        );
        // set the namespace
        $this->setNamespace(
            Faett_Channel_Serializer_Release_Abstract::REST_RELEASE
        );
    }

    /**
	 * Returns the link for the actual product with
	 * the requested version.
	 *
	 * @return Faett_Package_Model_Link The requested link
     */
    protected function _getLink()
    {
        // load and return the link
        return Mage::getModel('package/link')
            ->loadByProductIdAndVersion(
                $this->_package->getId(),
                $this->_package->getVersion()
            );
    }

    /**
	 * Returns the link title for the actual product with
	 * the requested version and the actual store.
	 *
	 * @return Faett_Package_Model_Link The requested link
	 */
    protected function _getLinkTitle()
    {
        // load the link title for the actual store ID
        return $this->_getLink()->getLinkTitle(
            Mage::app()->getStore()->getId()
        );
    }

    /**
	 * Adds the download link.
	 *
	 * @return void
	 */
    protected function _addDownloadLink(DOMDocument $doc, DOMNode $r)
    {
        $user = $this->getUser();

        // load the link
        $link = $this->_getLink();
        // if yes, use the unhashed link
        $downloadUrl = Mage::getBaseUrl('media').'package/files/links'.substr(
            $link->getLinkFile(),
            0,
            strlen($link->getLinkFile()) - 4
        );
        // create an element for the release's download location
        $g = $doc->createElement('g');
        // get the download URL depending on the customer's group
        $g->nodeValue = $downloadUrl;
        // append the element with the release's download location to the root element
        $r->appendChild($g);
    }

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
            $r = $doc->createElementNS(
            	$this->_namespace,
            	'r'
            );
            // add the schema to the root element
            $r->setAttributeNS(
            	'http://www.w3.org/2001/XMLSchema-instance',
            	'xsi:schemaLocation',
            	'http://pear.php.net/dtd/rest.release http://pear.php.net/dtd/rest.release.xsd'
            );
            // create an element for the package's name
            $p = $doc->createElement('p');
            $p->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'/channel/index/p/'.$this->_package->getPackageName()
            );
            $p->nodeValue = $this->_package->getPackageName();
            // append the element with the packages's name to the root element
            $r->appendChild($p);
            // create an element for the channel's name
            $c = $doc->createElement('c');
            $c->nodeValue = Mage::helper('channel')->getChannelName();
            // append the element with the channel's name to the root element
            $r->appendChild($c);
            // load the link and the link title
            $link = $this->_getLink();
            $linkTitle = $this->_getLinkTitle();
            // create an element for the version number
            $v = $doc->createElement('v');
            $v->nodeValue = $link->getVersion();
            // append the element with the version number to the root element
            $r->appendChild($v);
            // create an element for the state
            $st = $doc->createElement('st');
            $st->nodeValue = $link->getState();
            // append the element with the state to the root element
            $r->appendChild($st);
            // create an element for the licence
            $l = $doc->createElement('l');
            $l->nodeValue = $link->getLicence();
            // append the element with the licence to the root element
            $r->appendChild($l);
            // iterate over the package maintainers
            foreach ($this->_package->getMaintainers() as $maintainer) {
                // create an element for the maintainer
                $m = $doc->createElement('m');
                $m->nodeValue = $maintainer->getHandle();
                // append the element with the maintainer to the root element
                $r->appendChild($m);
                break;
            }
            // create an element for the release's summary
            $s = $doc->createElement('s');
            $s->nodeValue = $link->getSummary();
            // append the element with the release's summary to the root element
            $r->appendChild($s);
            // create an element for the release's description
            $d = $doc->createElement('d');
            $d->nodeValue = $link->getDescription();
            // append the element with the release's description to the root element
            $r->appendChild($d);
            // create an element for the release's release date
            $da = $doc->createElement('da');
            $da->nodeValue = $link->getReleaseDate();
            // append the element with the release's release date to the root element
            $r->appendChild($da);
            // create an element for the release's release notes
            $n = $doc->createElement('n');
            $n->nodeValue = $link->getNotes();
            // append the element with the release's release release notes to the root element
            $r->appendChild($n);
            // create an element for the release's package size
            $f = $doc->createElement('f');
            $f->nodeValue = $link->getPackageSize();
            // append the element with the release's package size to the root element
            $r->appendChild($f);
            // create an element for the link to the package's release directory
            $x = $doc->createElement('x');
            $x->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'package.'.$this->_package->getVersion().'.xml'
            );
            // append the XLink
            $r->appendChild($x);
            // add the download link
            $this->_addDownloadLink($doc, $r);
            // append the root element to the DOM tree
            $doc->appendChild($r);
            // return the XML document
            return $doc->saveXML();
        } catch(Exception $e) {
            return $e->getMessage();
        }
    }
}