<?php
/**
 * OpenSKOS
 *
 * LICENSE
 *
 * This source file is subject to the GPLv3 license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenSKOS
 * @package    OpenSKOS
 * @copyright  Copyright (c) 2016 ACDH
 * @author     Mateusz Żółtak
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 */

class Api_ConceptSchemeController extends OpenSKOS_Rest_Controller {
	public function init()
	{
		parent::init();
		$this->_helper->contextSwitch()->initContext('rdf');
	}
	
	public function headAction() {
		$this->_501('HEAD');	
	}
	
	public function indexAction() {
		$this->_501('INDEX');
	}

	public function getAction() {
		$this->view->conceptSchema = new DOMDocument;
		$apiBase = preg_replace('#/concept-scheme(/[^/]*)?$#', '', $_SERVER['SCRIPT_URI']);
		$id = $this->getRequest()->getParam('id');
		if($id == ''){
			throw new Zend_Controller_Exception('Missing required parameter `id`', 400);
		}

		// SKOS Concept Scheme
		$conceptSchemaURL = sprintf('%s/concept?id=%s', $apiBase, urlencode($id));
		$conceptSchemaRDF = @file_get_contents($conceptSchemaURL);
		if($conceptSchemaRDF == ''){
			throw new Zend_Controller_Exception('Concept schema with provided id does not exist', 400);
		}
		$this->view->conceptSchema->loadXml($conceptSchemaRDF);

		$conceptsURL = sprintf('%s/find-concepts/?rows=100100&q=inScheme:%%22%s%%22', $apiBase, urlencode($id));
		$conceptsRDF = @file_get_contents($conceptsURL);
		$concepts = new DOMDocument();
		$concepts->loadXml($conceptsRDF);

		$xpath = new DOMXPath($concepts);
		$xpath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
		$xpath->registerNamespace('skos', 'http://www.w3.org/2004/02/skos/core#');

		// SKOS collections
		$collections = array();
		foreach($xpath->query('//skos:inSkosCollection') as $collNode){
			$coll = $collNode->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource');
			if($coll != ''){
				$collections[$coll] = '';
			}
		}
		foreach(array_keys($collections) as $collId){
			$collURL = sprintf('%s/open-skos-collection?id=%s', $apiBase, urlencode($collId));
			$collRDF = file_get_contents($collURL);
			$coll = new DOMDocument();
			$coll->loadXml($collRDF);
			
			$collXPath = new DOMXPath($coll);
			$collXPath->registerNamespace('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
			$this->appendNodes($this->view->conceptSchema->documentElement, $collXPath->query('rdf:Description'));
		}

		// SKOS concepts
		$this->appendNodes($this->view->conceptSchema->documentElement, $xpath->query('//rdf:Description'));
	}

	public function postAction() {
		$this->_501('POST');
	}

	public function putAction() {
		$this->_501('PUT');
	}

	public function deleteAction() {
		$this->_501('DELETE');
	}

	private function appendNodes(DOMElement $target, DOMNodeList $nodes){
		foreach($nodes as $node){
			$node = $target->ownerDocument->importNode($node, true);
			$target->appendChild($node);
		}
	}

	/*
         * If file_get_contents() doesn't work with https urls on a given server
         */
	private function getCurl($url){
		$req = curl_init();
		curl_setopt($req, CURLOPT_HEADER, false);
		curl_setopt($req, CURLOPT_URL, $url);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		$results = curl_exec($req);
		curl_close($req);
		return $results;
	}
}

