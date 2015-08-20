<?php

require_once dirname(__DIR__) . '../../../library/EPIC/EPICHandleProxy.php';

class Api_OpenSkosCollectionController extends OpenSKOS_Rest_Controller {
	
	protected $model;
	
	public function init()
	{
		parent::init();
		$this->model = Api_Models_OpenSkosCollection::factory()->setQueryParams(
				$this->getRequest()->getParams()
		);
		$this->_helper->contextSwitch()
		->initContext($this->getRequest()->getParam('format', 'rdf'));
	
		if('html' == $this->_helper->contextSwitch()->getCurrentContext()) {
			//enable layout:
			$this->getHelper('layout')->enableLayout();
		}
	}
	
// 	public function init()
// 	{
// 		parent::init();
// 		$this->model = Api_Models_TryModel::factory()->setQueryParams(
// 				$this->getRequest()->getParams()
// 		);
// 		$this->_helper->contextSwitch()
// 					->addContext('rdf', array('xml', 'rdfo'))
//                       ->addActionContext('index', array('xml', 'rdf'))
//                       //->addActionContext('index', 'html')
//                       ->setAutoJsonSerialization(false)->initContext($this->getRequest()->getParam('format', 'rdf'));
	
// // 		if('html' == $this->_helper->contextSwitch()->getCurrentContext()) {
// // 			//enable layout:
// // 			$this->getHelper('layout')->enableLayout();
// // 		}
// 	}
	
	/*
	 * Returns the concepts that are in the openSkosCollection with the given "id" param as a uri
	 * if no concepts were found in case the argument SkosCollection's was a uri, the routine tries to find concepts for a SkosCollection with uuid as "id" param
	 */
	public function indexAction() {		
		if (null === ($id = $this->getRequest()->getParam('q'))) {
			$this->getResponse()
			->setHeader('X-Error-Msg', 'Missing required parameter `q`');
			throw new Zend_Controller_Exception('Missing required parameter `q`', 400);
		}
		
		$handleServerClient = EPICHandleProxy::getInstance();
		$handleResolverUrl = $handleServerClient->getResolver();
		$handleServerPrefix = $handleServerClient->getPrefix();
		
		$genericHandleUrlPart = $handleResolverUrl . $handleServerPrefix . "/";
		
		//$this->_helper->contextSwitch()->addActionContext('index', 'xml');
		
		$paramsArray = $this->getRequest()->getParams();
		$paramsArray['q'] = "inScheme:" . $id;
		$this->getRequest()->setParams($paramsArray);
		
		// first see if the id is meant as a URI
		// try and get all concepts that have $id as the inSkosCollection field..
		$concepts = $this->model->getConcepts("inSkosCollection:" . "\"" . $id . "\""); // self::solrEscape($id));
		if (count($concepts["response"]["docs"]) == 0) { // nothing found, try and see if id was meant as the UUID of the SkosCollection
			$skosCollection = $this->model->getConcepts("uuid:" . "\"" . $id . "\"");
			if (count($skosCollection["response"]["docs"]) > 0) { // should be 1 only if found..
				$concepts = $this->model->getConcepts("inSkosCollection:" . "\"" . $genericHandleUrlPart . $id . "\""); // self::solrEscape($id));
			}
			else {
				// sorry, no such SkosCollection found...
			}
		}
		
		$context = $this->_helper->contextSwitch()->getCurrentContext();
		if ($context === 'json' || $context === 'jsonp') {
			foreach ($concepts as $key => $val) {
				foreach ($val['docs'] as &$doc) unset($doc['xml']);
				$this->view->$key = $val;
			}
		} elseif ($context === 'xml') {
			$xpath = new DOMXPath($concepts);
			foreach ($xpath->query('/response/result/doc/str[@name="xml"]') as $node) {
				$node->parentNode->removeChild($node);
			}
			$this->view->response = $concepts;
		} else {
			$model = new OpenSKOS_Db_Table_Namespaces();
			$this->view->namespaces = $model->fetchPairs();
			$this->view->response = $concepts;
		}
	}
	
	/*
	 * Returns the openSkosCollection with the uri as id
	 */
	public function getAction() {
		
		if (null === ($id = $this->getRequest()->getParam('id'))) {
			$this->getResponse()
			->setHeader('X-Error-Msg', 'Missing required parameter `id`');
			throw new Zend_Controller_Exception('Missing required parameter `id`', 400);
		}
		//$concepts = $this->model->getConcepts("uuid:". self::solrEscape($id) . " AND class:SKOSCollection");
		//$concepts = $this->model->getConcepts("uuid:". $id . " AND class:SKOSCollection");
		$concepts = $this->model->getConcepts("uri:". $id . " AND class:SKOSCollection");
		
		
		//echo(count($concepts));
		
		$context = $this->_helper->contextSwitch()->getCurrentContext();
		if ($context === 'json' || $context === 'jsonp') {
			foreach ($concepts as $key => $val) {
				foreach ($val['docs'] as &$doc) unset($doc['xml']);
				$this->view->$key = $val;
			}
		} elseif ($context === 'xml') {
			$xpath = new DOMXPath($concepts);
			foreach ($xpath->query('/response/result/doc/str[@name="xml"]') as $node) {
				$node->parentNode->removeChild($node);
			}
			$this->view->response = $concepts;
		} else {
			$model = new OpenSKOS_Db_Table_Namespaces();
			$this->view->namespaces = $model->fetchPairs();
			$this->view->response = $concepts;
		}
	}
	
	public function postAction() {
		$this->_501('post');
	}
	
	public function putAction() {
		$this->_501('put');
	}
	
	public function deleteAction() {
		$this->_501('delete');
	}
	
	public function headAction() {
		$this->_501('head');
	}
	
	protected static function solrEscape($text) {
		$match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
		$replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
		$text = str_replace($match, $replace, $text);
		if(!preg_match("/ /",$text)) {
			$string = "\"".$text."\"";
		}
		return $text;
	}
	
}