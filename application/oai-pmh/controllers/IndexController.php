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
* @copyright  Copyright (c) 2011 Pictura Database Publishing. (http://www.pictura-dp.nl)
* @author     Mark Lindeman
* @license    http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
*/

class OaiPmh_IndexController extends OpenSKOS_Rest_Controller
{
	public function init()
	{
		$this->getHelper('layout')->disableLayout();
		$this->getResponse()->setHeader('Content-Type' , 'text/xml; charset=utf8');
		
	}
	
	public function indexAction() 
	{
		require_once APPLICATION_PATH . '/' . $this->getRequest()->getModuleName() .'/models/OaiPmh.php';
		$this->view->responseDate = date(OaiPmh::XS_DATETIME_FORMAT); 
			
		$oai = new OaiPmh($this->getRequest()->getParams(), $this->view);
		$oai->setBaseUrl('http:'.($_SERVER['SERVER_PORT']==443?'s':'') . '//'
			.$_SERVER['HTTP_HOST']
			. $this->getFrontController()->getRouter()->assemble(array())
		);
		$this->view->oai = $oai;
		
	}
	
	public function getAction() 
	{
		$this->_501('GET');
	}
	
	public function postAction() {
		$this->_501('POST');
	}

	public function putAction() {
		$this->_501('POST');
	}

	public function deleteAction() {
		$this->_501('DELETE');
	}
	
	public function headAction() {		
	}
}